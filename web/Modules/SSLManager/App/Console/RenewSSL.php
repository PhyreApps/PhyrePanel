<?php

namespace Modules\SSLManager\App\Console;

use App\Jobs\ApacheBuild;
use App\Models\CronJob;
use App\Models\Domain;
use Illuminate\Console\Command;
use Modules\SSLManager\App\Jobs\SecureDomain;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RenewSSL extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ssl-manager:renew-ssl';

    /**
     * The console command description.
     */
    protected $description = 'Renew SSL certificates';

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

        $this->_checkForAutoRenewalCron();

        $getDomains = Domain::where('status', Domain::STATUS_ACTIVE)
          //  ->where('domain', 'pgd-retirment-invitation.clould.microweber.me')
            ->get();
        if ($getDomains->count() > 0) {
            foreach ($getDomains as $domain) {
                $checkDomainStatus = $this->_checkForSSL($domain->domain);
                if ($checkDomainStatus) {
                    $this->info('SSL certificate for ' . $domain->domain . ' is valid');
                } else {
                    $this->info('SSL certificate for ' . $domain->domain . ' is expired');
                    try {
                        $this->_renewSSL($domain);
                    } catch (\Exception $e) {
                        $this->error($e->getMessage());
                    }
                }
            }

            // Rebuild Apache configuration
            $this->info('Rebuilding Apache configuration');
            $apacheBuild = new ApacheBuild(true);
            $apacheBuild->handle();
        }

    }

    private function _renewSSL($domain)
    {
        // Renew SSL
        $this->info('Renewing SSL certificate for ' . $domain->domain);



        try {
            $secureDomain = new SecureDomain($domain->id);
            $secureDomain->handle();
            $this->info('SSL certificate renewed for ' . $domain->domain);
        } catch (\Exception $e) {
            $this->error('Error renewing SSL certificate: ' . $e->getMessage());
        }

    }


    private function _checkForSSL($domain)
    {

        // Check with CURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://" . $domain);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $data = curl_exec($ch);

        $sslInfo = curl_getinfo($ch);

        if ($sslInfo['http_code'] == 200) {
            return true;
        } else {
            return false;
        }


    }

    public function _checkForAutoRenewalCron()
    {
        $cronJobCommand = 'phyre-php /usr/local/phyre/web/artisan ssl-manager:renew-ssl';
        $findCronJob = CronJob::where('command', $cronJobCommand)->first();
        if (! $findCronJob) {
            $cronJob = new CronJob();
            $cronJob->schedule = '0 0 * * *';
            $cronJob->command = $cronJobCommand;
            $cronJob->user = 'root';
            $cronJob->save();
        }
    }

}
