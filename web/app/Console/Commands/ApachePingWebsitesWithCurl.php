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
    protected $signature = 'apache:ping-websites-with-curl';

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
        $findCustomer = \App\Models\Customer::first();
        $findHostingPlan = \App\Models\HostingPlan::where('id',2)->first();

        for ($i = 0; $i <= 50000; $i++) {
            $newSubscription = new \App\Models\HostingSubscription();
            $newSubscription->customer_id = $findCustomer->id;
            $newSubscription->hosting_plan_id = $findHostingPlan->id;
            $newSubscription->domain = 'next-'.rand(11111,99999).'server-1-'.$i.rand(11111,99999).'.test.multiweber.com';
            $newSubscription->save();
        }

        return;

        // Retrieve all website configurations from the database
        $websiteConfigs = Domain::get();

        foreach ($websiteConfigs as $config) {
            $domain = $config->domain;

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
