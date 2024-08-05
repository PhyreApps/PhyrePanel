<?php

namespace Modules\LetsEncrypt\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class LetsencryptAuthenticatorHook extends Command
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


        // .well-known/acme-challenge

    }
}
