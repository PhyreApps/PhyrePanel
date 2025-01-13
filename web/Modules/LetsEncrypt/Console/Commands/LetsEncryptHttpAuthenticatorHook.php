<?php

namespace Modules\LetsEncrypt\Console\Commands;

use App\Models\Domain;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class LetsEncryptHttpAuthenticatorHook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    protected $signature = 'phyre:letsencrypt-http-authenticator-hook {--certbot-domain=} {--certbot-token=} {--certbot-validation=}';

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
        $this->info('Letsencrypt Authenticator Hook');

        $certbotToken = $this->option('certbot-token');
        $certbotValidation = $this->option('certbot-validation');
        $certbotDomain = $this->option('certbot-domain');

        $certbotDomain = str_replace('www.', '', $certbotDomain);

        $findDomain = Domain::where('domain', $certbotDomain)->first();
        if (!$findDomain) {
            $this->error('Domain not found:' . $certbotDomain);
            return;
        }
        if (!is_dir($findDomain->domain_public)) {
            $this->error('Domain public directory not found');
            return;
        }

        $acmeChallangePath = $findDomain->domain_public . '/.well-known/acme-challenge';
        if (!is_dir($acmeChallangePath)) {
            shell_exec('mkdir -p ' . $acmeChallangePath);
        }
        $authFilePath = $acmeChallangePath . '/' . $certbotToken;
        file_put_contents($authFilePath, $certbotValidation);

        $this->info('Auth file created: ' . $authFilePath);

    }
}
