<?php

namespace Modules\Microweber\App\Console\Commands;

use App\Models\Domain;
use Illuminate\Console\Command;
use MicroweberPackages\SharedServerScripts\MicroweberReinstaller;
use Modules\Microweber\App\Actions\MicroweberScanner;
use Modules\Microweber\App\Models\MicroweberInstallation;

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
        $getMwInstallations = MicroweberInstallation::all();
        if ($getMwInstallations->count() > 0) {
            foreach ($getMwInstallations as $mwInstallation) {

                $domain = $mwInstallation->domain()->first();
                if (!$domain) {
                    $this->info('Domain not found: ' . $mwInstallation->domain_id);
                    continue;
                }

                $this->info('Repair domain: ' . $domain->domain);

                $microweberReinstall = new MicroweberReinstaller();
                $microweberReinstall->setSymlinkInstallation();
                $microweberReinstall->setChownUser($domain->domain_username);
                $microweberReinstall->setPath($mwInstallation->installation_path);
                $microweberReinstall->setSourcePath(config('microweber.sharedPaths.app'));

                $reinstallStatus = $microweberReinstall->run();
            }
        }
    }
}
