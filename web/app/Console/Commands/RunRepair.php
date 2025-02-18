<?php

namespace app\Console\Commands;

use App\Actions\GetLinuxUser;
use App\ApacheParser;
use App\Jobs\ApacheBuild;
use App\Models\Backup;
use App\Models\Database;
use App\Models\Domain;
use App\Models\HostingSubscription;
use App\PhyreConfig;
use App\UniversalDatabaseExecutor;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use function Symfony\Component\String\s;

class RunRepair extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:run-repair';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        // Find broken domains
//        $findBrokenDomains = Domain::where('status', Domain::STATUS_BROKEN)->get();
//        if ($findBrokenDomains) {
//            foreach ($findBrokenDomains as $brokenDomain) {
//                $brokenDomain->status = Domain::STATUS_ACTIVE;
//                $brokenDomain->saveQuietly();
//            }
//        }

        $this->fixDatabaseRootUsers();

        $this->fixPhpMyAdmin();

        // Overwrite supervisor config file
        $minWorkersCount = 1;
        $workersCount = (int) setting('general.supervisor_workers_count');
        if ($workersCount < $minWorkersCount) {
            $workersCount = $minWorkersCount;
        }
        $supervisorConf = view('actions.samples.ubuntu.supervisor-conf', [
            'workersCount' => $workersCount
        ])->render();

        // Overwrite supervisor config file
        file_put_contents('/etc/supervisor/conf.d/phyre.conf', $supervisorConf);

        // Restart supervisor
        shell_exec('service supervisor restart');

        // Check supervisor config file
        $checkSupervisorStatus = shell_exec('service supervisor status');
        if (strpos($checkSupervisorStatus, 'active (running)') !== false) {
           $this->info('Supervisor is running');
        } else {
            $this->info('Supervisor is not running. Please check supervisor status');
        }

        $this->fixApacheErrors();

        echo shell_exec('phyre-php /usr/local/phyre/web/artisan ssl-manager:renew-ssl');

    }

    public function fixPhpMyAdmin()
    {
        $this->info('Fix phpMyAdmin');

        // Download PHPMyAdmin
        shell_exec('mkdir -p /usr/share/phpmyadmin');
        shell_exec('rm -rf /usr/share/phpmyadmin/*');
        shell_exec('wget https://files.phpmyadmin.net/phpMyAdmin/5.2.1/phpMyAdmin-5.2.1-all-languages.zip -O /tmp/phpMyAdmin-5.2.1-all-languages.zip');
        shell_exec('unzip /tmp/phpMyAdmin-5.2.1-all-languages.zip -d /usr/share/phpmyadmin');
        shell_exec('mv /usr/share/phpmyadmin/phpMyAdmin-5.2.1-all-languages/* /usr/share/phpmyadmin');
        shell_exec('rm -rf /usr/share/phpmyadmin/phpMyAdmin-5.2.1-all-languages');
        shell_exec('rm -f /tmp/phpMyAdmin-5.2.1-all-languages.zip');

        $ssoContent = file_get_contents('/usr/local/phyre/web/server/phpmyadmin/phyre-sso.php.dist');
        if ($ssoContent) {
            file_put_contents('/usr/share/phpmyadmin/phyre-sso.php', $ssoContent);
        }

        $configContent = file_get_contents('/usr/local/phyre/web/server/phpmyadmin/config.inc.php.dist');
        if ($configContent) {
            file_put_contents('/usr/share/phpmyadmin/config.inc.php', $configContent);
        }

        $sessionDir = '/usr/local/phyre/data/sessions';
        if (!is_dir($sessionDir)) {
            shell_exec('mkdir -p ' . $sessionDir);
        }
    }

    public function fixDatabaseRootUsers()
    {
        $findDatabases = Database::all();
        if ($findDatabases->count() > 0) {
            foreach ($findDatabases as $database) {
                if ($database->is_remote_database_server == 1) {
                    // TODO
                    continue;
                }

                $findHostingSubscription = HostingSubscription::where('id', $database->hosting_subscription_id)->first();
                if (!$findHostingSubscription) {
                    continue;
                }

                $universalDatabaseExecutor = new UniversalDatabaseExecutor(
                    PhyreConfig::get('MYSQL_HOST', '127.0.0.1'),
                    PhyreConfig::get('MYSQL_PORT', 3306),
                    PhyreConfig::get('MYSQL_ROOT_USERNAME'),
                    PhyreConfig::get('MYSQL_ROOT_PASSWORD'),
                );
                $universalDatabaseExecutor->fixPasswordPolicy();

                // Check main database user exists
                $mainDatabaseUser = $universalDatabaseExecutor->getUserByUsername($findHostingSubscription->system_username);
                if (!$mainDatabaseUser) {
                    $createMainDatabaseUser = $universalDatabaseExecutor->createUser($findHostingSubscription->system_username, $findHostingSubscription->system_password);
                    if (!isset($createMainDatabaseUser['success'])) {
                        throw new \Exception($createMainDatabaseUser['message']);
                    }
                }

                $databaseName = Str::slug($database->database_name, '_');
                $databaseName = $database->database_name_prefix . $databaseName;
                $databaseName = strtolower($databaseName);

                $universalDatabaseExecutor->userGrantPrivilegesToDatabase($findHostingSubscription->system_username, [$databaseName]);

            }
        }
    }

    public function fixApacheErrors()
    {
        $findHostingSubscriptions = HostingSubscription::get();
        if ($findHostingSubscriptions) {
            foreach ($findHostingSubscriptions as $hostingSubscription) {
                $getLinuxUser = new GetLinuxUser();
                $getLinuxUser->setUsername($hostingSubscription->system_username);
                $getLinuxUserStatus = $getLinuxUser->handle();
                if (!$getLinuxUserStatus) {
                    $findDomains = Domain::where('hosting_subscription_id', $hostingSubscription->id)->get();
                    if ($findDomains) {
                        foreach ($findDomains as $domain) {
                            $domain->status = Domain::STATUS_BROKEN;
                            $domain->saveQuietly();
                            $this->error('Turn on maintenance mode: ' . $domain->domain);
                        }
                    }
                    $this->error('User not found: ' . $hostingSubscription->system_username);
                    continue;
                }
            }
        }

        // Rebuild apache config
        $apacheBuild = new ApacheBuild();
        $apacheBuild->handle();

        $checkApacheStatus = shell_exec('service apache2 status');
        if (strpos($checkApacheStatus, 'Syntax error on line') !== false) {

            $this->error('Apache syntax error found');
            $this->error($checkApacheStatus);

            $apacheErrorLine = null;
            preg_match('/Syntax error on line (\d+)/', $checkApacheStatus, $matchApacheErrorLine);
            if (isset($matchApacheErrorLine[1]) && is_numeric($matchApacheErrorLine[1])) {
                $apacheErrorLine = $matchApacheErrorLine[1];
            }

            $apacheBrokenVirtualHosts = [];

            $parser = new ApacheParser();
            $configNode = $parser->parse('/etc/apache2/apache2.conf');
            $configChildren = $configNode->getChildren();
            foreach ($configChildren as $child) {
                if ($child->getName() == 'VirtualHost') {
                    $virtualHost = [
                        'startLine' => $child->getStartLine(),
                        'endLine' => $child->getEndLine(),
                        'content' => $child->getContent()
                    ];
                    $childChildren = $child->getChildren();
                    if (isset($childChildren[0])) {
                        foreach ($childChildren as $childChild) {
                            $virtualHost[$childChild->getName()] = $childChild->getContent();
                        }
                    }
                    if ($child->getStartLine() <= $apacheErrorLine && $child->getEndLine() >= $apacheErrorLine) {
                        $apacheBrokenVirtualHosts[] = $virtualHost;
                    }
                }
            }

            if (count($apacheBrokenVirtualHosts) > 0) {

                $this->error('Broken virtual hosts found');

                foreach ($apacheBrokenVirtualHosts as $brokenVirtualHost) {
                    $this->error('Virtual host found: ' . $brokenVirtualHost['ServerName']);
                    $this->error('Turn on maintenance mode: ' . $brokenVirtualHost['ServerName']);
                    $findDomain = Domain::where('domain', $brokenVirtualHost['ServerName'])->first();
                    if ($findDomain) {
                        $findDomain->status = Domain::STATUS_BROKEN;
                        $findDomain->save();
                    }
                }

                $this->info('Run apache build...');

                $apacheBuild = new ApacheBuild();
                $apacheBuild->handle();
            }
        }

        shell_exec('service apache2 restart');
        $newCheckApacheStatus = shell_exec('service apache2 status');
        if (Str::contains($newCheckApacheStatus, 'active (running)')) {
            $this->info('Apache is running');
        } else {
            $this->info('Apache is not running. Please check apache status');
        }

    }
}
