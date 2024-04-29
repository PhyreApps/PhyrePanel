<?php

namespace App\Console\Commands;

use App\Models\Domain;
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
        $getAllDomains = Domain::all();
        if ($getAllDomains->count() > 0) {
            foreach ($getAllDomains as $domain) {
                $this->info('Repair domain: ' . $domain->domain);
                $domain->configureVirtualHost();
            }
        }
    }
}
