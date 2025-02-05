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
        $getAllDomains = Domain::whereNot('status', '<=>', 'broken')->get();
        $virtualHosts = [];


        file_put_contents(storage_path('logs/apache2.log'),1111);

        foreach ($getAllDomains as $domain) {
            $isBroken = false;
//            $findHostingSubscription = \App\Models\HostingSubscription::where('id', $domain->hosting_subscription_id)
//                ->first();
//            // check is valid domain
//            if (!$findHostingSubscription) {
//                $isBroken = true;
//            }
            if ($domain->status === 'broken') {
                continue;
            }
            // check is valid domain
            if (!filter_var($domain->domain, FILTER_VALIDATE_DOMAIN)) {
                $isBroken = true;
            }

            if ($isBroken) {
                // mark domain as broken
                $domain->status = 'broken';
                $domain->save();
                continue;
            }


            /* @var  Domain $domain */
            $virtualHostSettings = $domain->configureVirtualHost($this->fixPermissions);

            if (!$virtualHostSettings) {
                // mark domain as broken
                $domain->status = 'broken';
                $domain->save();
                continue;
            }

            if (isset($virtualHostSettings['virtualHostSettings'])) {
                $virtualHosts[] = $virtualHostSettings['virtualHostSettings'];
            }
            if (isset($virtualHostSettings['virtualHostSettingsWithSSL']) and $virtualHostSettings['virtualHostSettingsWithSSL']) {
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
            if (isset($domainVirtualHost['virtualHostSettingsWithSSL']) and $domainVirtualHost['virtualHostSettingsWithSSL']) {
                $virtualHosts[] = $domainVirtualHost['virtualHostSettingsWithSSL'];
            }
        }

        $apache2 = view('actions.samples.ubuntu.apache2-conf-build', [
            'virtualHosts' => $virtualHosts,
            'serverName' => setting('general.master_domain') ?? 'localhost',
        ])->render();

        $apache2 = preg_replace('~(*ANY)\A\s*\R|\s*(?!\r\n)\s$~mu', '', $apache2);

        file_put_contents('/etc/apache2/apache2-process.conf', $apache2);

        shell_exec('cp /etc/apache2/apache2-process.conf /etc/apache2/apache2.conf');

        shell_exec('systemctl reload apache2'); // IMPORTANT: MUST BE RELOAD! NOT RESTART!

    }
}
