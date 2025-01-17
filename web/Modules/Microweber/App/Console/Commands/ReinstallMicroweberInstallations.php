<?php

namespace Modules\Microweber\App\Console\Commands;

use App\Models\Domain;
use App\Models\HostingSubscription;
use Illuminate\Console\Command;
use MicroweberPackages\SharedServerScripts\MicroweberReinstaller;
use Modules\Microweber\App\Actions\MicroweberScanner;
use Modules\Microweber\App\Models\MicroweberInstallation;
use Modules\Microweber\Jobs\UpdateWhitelabelToWebsites;

class ReinstallMicroweberInstallations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'microweber:reinstall-installations';

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
        $sharedServerScriptsPath = '/usr/local/phyre/web/vendor/microweber-packages/shared-server-scripts/shell-scripts/';
        shell_exec('chmod +x ' . $sharedServerScriptsPath . 'chown_installed_app.sh');

        $updateWhitelabel = new UpdateWhitelabelToWebsites();
        $updateWhitelabel->handle();

        $getMwInstallations = MicroweberInstallation::all();
        if ($getMwInstallations->count() > 0) {
            foreach ($getMwInstallations as $mwInstallation) {

                $domain = $mwInstallation->domain()->first();
                if (!$domain) {
                    $this->info('Domain not found: ' . $mwInstallation->domain_id);
                    continue;
                }
                $findHostingSubscription = HostingSubscription::where('id', $domain->hosting_subscription_id)->first();
                if (!$findHostingSubscription) {
                    $this->info('Hosting subscription not found: ' . $domain->hosting_subscription_id);
                    continue;
                }

                $this->info('Repair domain: ' . $domain->domain);

                $microweberReinstall = new MicroweberReinstaller();
                $microweberReinstall->setSymlinkInstallation();
                $microweberReinstall->setChownUser($findHostingSubscription->system_username);
                $microweberReinstall->setPath($mwInstallation->installation_path);
                $microweberReinstall->setSourcePath(config('microweber.sharedPaths.app'));

                $reinstallStatus = $microweberReinstall->run();
            }
        }
    }
}
