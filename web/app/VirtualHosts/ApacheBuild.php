<?php

namespace App\VirtualHosts;

use App\MasterDomain;
use App\Models\Domain;

class ApacheBuild
{

    public $fixPermissions = false;

    public function fixPermissions()
    {
        $this->fixPermissions = true;
    }

    public function build()
    {
        $getAllDomains = Domain::all();
        $virtualHosts = [];
        foreach ($getAllDomains as $domain) {
            $virtualHostSettings = $domain->configureVirtualHost();
            if (isset($virtualHostSettings['virtualHostSettings'])) {
                $virtualHosts[] = $virtualHostSettings['virtualHostSettings'];
            }
            if (isset($virtualHostSettings['virtualHostSettingsWithSSL'])) {
                $virtualHosts[] = $virtualHostSettings['virtualHostSettingsWithSSL'];
            }
        }

        $apache2 = view('actions.samples.ubuntu.apache2-conf-build', [
            'virtualHosts' => $virtualHosts
        ])->render();

        file_put_contents('/etc/apache2/apache2.conf', $apache2);

        shell_exec('systemctl reload apache2');

    }

}
