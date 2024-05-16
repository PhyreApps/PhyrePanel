<?php

namespace Modules\Microweber\App\Actions;

use App\Models\Domain;
use MicroweberPackages\SharedServerScripts\MicroweberInstallationsScanner;
use Modules\Microweber\App\Models\MicroweberInstallation;

class MicroweberScanner
{
    public function handle()
    {
        $findDomains = Domain::all();
        foreach ($findDomains as $domain) {

            if (!is_dir($domain->domain_public)) {
                $deleteInstallation = MicroweberInstallation::where('domain_id', $domain->id)
                    ->get();
                if ($deleteInstallation != null) {
                    foreach ($deleteInstallation as $delete) {
                        $delete->delete();
                    }
                }
                continue;
            }

            $scan = new MicroweberInstallationsScanner();
            $scan->setPath($domain->domain_public);

            $installations = $scan->scanRecusrive();

            if (! empty($installations)) {
                foreach ($installations as $installation) {

                    $findInstallation = MicroweberInstallation::where('installation_path', $installation['path'])
                        ->where('domain_id', $domain->id)
                        ->first();

                    if (! $findInstallation) {
                        $findInstallation = new MicroweberInstallation();
                        $findInstallation->domain_id = $domain->id;
                        $findInstallation->installation_path = $installation['path'];
                    }

                    $findInstallation->app_version = $installation['version'];
                    if (isset($installation['app_details']['template'])) {
                        $findInstallation->template = $installation['app_details']['template'];
                    }

                    if ($installation['is_symlink']) {
                        $findInstallation->installation_type = 'symlink';
                    } else {
                        $findInstallation->installation_type = 'standalone';
                    }

                    $findInstallation->save();

                }
            }
        }

        $getAppInstallations = MicroweberInstallation::get();
        if ($getAppInstallations != null) {
            foreach ($getAppInstallations as $appInstallation) {
                if (! is_file($appInstallation['installation_path'].'/config/microweber.php')) {
                    $appInstallation->delete();
                }
            }
        }
    }
}
