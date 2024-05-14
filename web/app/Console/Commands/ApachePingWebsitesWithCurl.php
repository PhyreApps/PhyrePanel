<?php

namespace App\Console\Commands;

use App\Models\Domain;
use Illuminate\Console\Command;

class ApachePingWebsitesWithCurl extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:apache-ping-websites-with-curl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test if response is 200 for all websites';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $getDomains = Domain::get();

        foreach ($getDomains as $domainData) {

            $domain = $domainData->domain;

            $cmd = "curl -s -o /dev/null -w '%{http_code}' http://$domain";
            $response = shell_exec($cmd);
            if ($response == 200) {
                $this->info("Website $domain is up and running");
            } else {
                $this->warn("Website $domain is down");
            }

        }

        return 0;
    }
}
