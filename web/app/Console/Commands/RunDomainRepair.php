<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\VirtualHosts\ApacheBuild;
use Illuminate\Console\Command;

class RunDomainRepair extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:run-domain-repair';

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
        $apache = new ApacheBuild();
        $apache->fixPermissions();
        $apache->build();
    }
}
