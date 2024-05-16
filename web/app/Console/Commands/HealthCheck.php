<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class HealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:health-check';

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
        $this->info('Health check...');

        // Check supervisor
        $this->info('Checking supervisor...');
        $supervisorStatus = shell_exec('service supervisor status');
        if (Str::contains($supervisorStatus, 'active (running)')) {
            $this->info('Supervisor is running');
        } else {
            $this->error('Supervisor is not running');
            $this->info('Starting supervisor...');
            shell_exec('service supervisor start');
        }

        // Check Apache2
        $this->info('Checking Apache2...');
        $apache2Status = shell_exec('service apache2 status');
        if (Str::contains($apache2Status, 'active (running)')) {
            $this->info('Apache2 is running');
        } else {
            $this->error('Apache2 is not running');
            $this->info('Starting Apache2...');
            shell_exec('service apache2 start');
        }

    }
}
