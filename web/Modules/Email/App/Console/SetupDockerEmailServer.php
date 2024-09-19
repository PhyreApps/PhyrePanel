<?php

namespace Modules\Email\App\Console;

use App\Models\DomainSslCertificate;
use App\PhyreBlade;
use App\PhyreConfig;
use App\UniversalDatabaseExecutor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Blade;
use Modules\LetsEncrypt\Models\LetsEncryptCertificate;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SetupDockerEmailServer extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'email:setup-docker-email-server';

    /**
     * The console command description.
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $sslPaths = [];
        $findSSL = DomainSslCertificate::where('domain', setting('email.hostname'))->first();
        if ($findSSL) {
            $getSSLPaths = $findSSL->getSSLFiles();
            if ($getSSLPaths) {
                $sslPaths = $getSSLPaths;
            }
        }

        $postfixMainCf = PhyreBlade::render('email::server.postfix.main.cf', [
            'hostName' => setting('email.hostname'),
            'domain' => setting('email.domain'),
            'sslPaths' => $sslPaths,
        ]);

        file_put_contents('/etc/postfix/main.cf', $postfixMainCf);

        $postfixMasterCf = PhyreBlade::render('email::server.postfix.master.cf');
        file_put_contents('/etc/postfix/master.cf', $postfixMasterCf);

        shell_exec('systemctl restart dovecot');
        shell_exec('systemctl restart postfix');




//        $universalDatabaseExecutor = new UniversalDatabaseExecutor(
//            PhyreConfig::get('MYSQL_HOST', '127.0.0.1'),
//            PhyreConfig::get('MYSQL_PORT', 3306),
//            PhyreConfig::get('MYSQL_ROOT_USERNAME'),
//            PhyreConfig::get('MYSQL_ROOT_PASSWORD'),
//        );
//
     //   $createDb = $universalDatabaseExecutor->createDatabase('phyre_postfix');
//        dd($createDb);
//        $universalDatabaseExecutor->fixPasswordPolicy();
//        $createUser = $universalDatabaseExecutor->createUser('phyre_postfix', 'phyre_postfix_password');
//        dd($createUser);

//        $universalDatabaseExecutor->userGrantPrivilegesToDatabase('phyre_postfix', [
//            'phyre_postfix'
//        ]);


    }

    public function checkDNSValidation()
    {

        // exec: dig @1.1.1.1 +short MX allsidepixels.com
        // output: 10 mail.allsidepixels.com

        // exec: dig @1.1.1.1 +short A mail.allsidepixels.com
        // output: 49.13.13.211

        // exec: dig @1.1.1.1 +short -x 49.13.13.211
        // output: mail.allsidepixels.com

    }
}
