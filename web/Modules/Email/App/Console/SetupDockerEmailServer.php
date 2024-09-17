<?php

namespace Modules\Email\App\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Blade;
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

        $moduleServerConfigTemplatesPath = '/usr/local/phyre/web/Modules/Email/server/docker/';
        $dockerComposeYaml = file_get_contents($moduleServerConfigTemplatesPath . 'docker-compose.yaml');
        $dockerComposeYaml = Blade::render($dockerComposeYaml, [
            'containerName' => 'phyre-mail-server',
            'hostName'=> 'mail.server11.microweber.me',
            'workPath' => $workPath,
        ]);
        shell_exec('mkdir -p ' . $workPath);
        file_put_contents($workPath . '/docker-compose.yaml', $dockerComposeYaml);

     //   dd(shell_exec('docker-compose -f ' . $workPath . '/docker-compose.yaml up -d'));


        dd($dockerComposeYaml);

    }
}
