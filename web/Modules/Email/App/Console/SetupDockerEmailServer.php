<?php

namespace Modules\Email\App\Console;

use App\Models\DomainSslCertificate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Blade;
use Modules\LetsEncrypt\Models\LetsEncryptCertificate;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SetupDockerEmailServer extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'email:setup-docker-email-server';

    /**
     * The console command description.
     */
    protected $description = 'Command description.';

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
        $this->info('Setting up email server...');

        $workPath = '/usr/local/phyre/email/docker';

        $domain = 'allsidepixels.com';

        $moduleServerConfigTemplatesPath = '/usr/local/phyre/web/Modules/Email/server/docker/';
        $dockerComposeYaml = file_get_contents($moduleServerConfigTemplatesPath . 'docker-compose.yaml');
        $dockerComposeYaml = Blade::render($dockerComposeYaml, [
            'containerName' => 'phyre-mail-server',
            'hostName'=> 'mail.'.$domain,
            'workPath' => $workPath,
        ]);
        shell_exec('mkdir -p ' . $workPath);
        file_put_contents($workPath . '/docker-compose.yaml', $dockerComposeYaml);

        $ssl = DomainSslCertificate::where('domain', 'mail.'.$domain)->first();
        if ($ssl) {
            shell_exec('mkdir -p ' . $workPath . '/docker-data/acme-companion/certs/' . $domain);
            file_put_contents($workPath . '/docker-data/acme-companion/certs/' . $domain . '/fullchain.pem', $ssl->certificate_chain);
            file_put_contents($workPath . '/docker-data/acme-companion/certs/' . $domain . '/privkey.pem', $ssl->private_key);
        }



     //   dd(shell_exec('docker-compose -f ' . $workPath . '/docker-compose.yaml up -d'));

        // after compose you must create the email account

        //docker exec -ti c2d4fec32239 setup email add peter@allsidepixels.com passwd123

        //docker exec -ti c2d4fec32239 setup email add boris@allsidepixels.com passwd123

//        docker exec -it c2d4fec32239 setup config dkim


        //ufw allow 25
        //ufw allow 587
        //ufw allow 465

        dd($dockerComposeYaml);

    }

    public function checkDNSValidation()
    {

        // exec: dig @1.1.1.1 +short MX allsidepixels.com
        // output: 10 mail.allsidepixels.com

        // exec: dig @1.1.1.1 +short A mail.allsidepixels.com
        // output: 49.13.13.211

        // exec: dig @1.1.1.1 +short -x 49.13.13.211
        // output: mail.allsidepixels.com

    }
}
