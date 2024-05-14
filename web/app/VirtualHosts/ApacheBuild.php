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


        // Make master domain virtual host
        if (!empty(setting('general.master_domain'))) {
            // Make master domain virtual host
            $masterDomain = new MasterDomain();
            $domainVirtualHost = $masterDomain->configureVirtualHost();
            if (isset($domainVirtualHost['virtualHostSettings'])) {
                $virtualHosts[] = $domainVirtualHost['virtualHostSettings'];
            }
            if (isset($domainVirtualHost['virtualHostSettingsWithSSL'])) {
                $virtualHosts[] = $domainVirtualHost['virtualHostSettingsWithSSL'];
            }
        }

        // Make wildcard domain virtual host
        $wildcardDomain = setting('general.wildcard_domain');
        if (!empty($wildcardDomain)) {
            // Make wildcard domain virtual host
            $masterDomain = new MasterDomain();
            $masterDomain->domain = $wildcardDomain;
            $domainVirtualHost = $masterDomain->configureVirtualHost();
            if (isset($domainVirtualHost['virtualHostSettings'])) {
                $virtualHosts[] = $domainVirtualHost['virtualHostSettings'];
            }
            if (isset($domainVirtualHost['virtualHostSettingsWithSSL'])) {
                $virtualHosts[] = $domainVirtualHost['virtualHostSettingsWithSSL'];
            }
        }

        $apache2 = view('actions.samples.ubuntu.apache2-conf-build', [
            'virtualHosts' => $virtualHosts
        ])->render();

        file_put_contents('/etc/apache2/apache2.conf', $apache2);

        shell_exec('systemctl reload apache2');

    }

}
