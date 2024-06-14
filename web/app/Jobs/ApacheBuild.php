<?php

namespace App\Jobs;

use App\MasterDomain;
use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ApacheBuild implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $fixPermissions = false;

    /**
     * Create a new job instance.
     */
    public function __construct($fixPermissions = false)
    {
        $this->fixPermissions = $fixPermissions;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $getAllDomains = Domain::whereNot('status','<=>', 'broken')->get();
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

        $apache2 = preg_replace('~(*ANY)\A\s*\R|\s*(?!\r\n)\s$~mu', '', $apache2);

        file_put_contents('/etc/apache2/apache2.conf', $apache2);

        shell_exec('systemctl reload apache2'); // IMPORTANT: MUST BE RELOAD! NOT RESTART!

    }
}
