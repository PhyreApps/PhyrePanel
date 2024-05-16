<?php

namespace Tests\Unit;

use App\Jobs\ApacheBuild;
use App\Models\HostingPlan;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Modules\Microweber\Filament\Clusters\Microweber\Pages\Version;
use Modules\Microweber\Jobs\DownloadMicroweber;
use Tests\Feature\Api\ActionTestCase;

class MWHSCreateTest extends ActionTestCase
{
    function testCreateInstallation()
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        Artisan::call('phyre:install-module Microweber');

        $downloadMicroweber = new DownloadMicroweber();
        $downloadMicroweber->handle();

        $random = rand(1000, 9999);
        $callHostingPlanStoreResponse = $this->callApiAuthorizedRouteAction('api.hosting-plans.store',[
            'name' => 'Unit Test Microweber Hosting Plan #' . $random,
            'description' => 'Unit Test Microweber Hosting Plan Description',
            'disk_space' => 1000,
            'bandwidth' => 1000,
            'default_server_application_type' => 'apache_php',
            'default_server_application_settings' => [
                'php_version' => '8.2',
            ],
            'additional_services' => ['microweber'],
            'default_database_server_type' => 'internal',

        ])->json();


        $this->assertArrayHasKey('status', $callHostingPlanStoreResponse);
        $this->assertTrue($callHostingPlanStoreResponse['status'] == 'ok');
        $hostingPlanId = $callHostingPlanStoreResponse['data']['hostingPlan']['id'];
        $this->assertIsInt($hostingPlanId);

        $callHostingPlanResponse = $this->callApiAuthorizedRouteAction('api.hosting-plans.index')->json();
        $this->assertArrayHasKey('status', $callHostingPlanResponse);
        $this->assertTrue($callHostingPlanResponse['status'] == 'ok');
        $this->assertArrayHasKey('data', $callHostingPlanResponse);
        $this->assertArrayHasKey('hostingPlans', $callHostingPlanResponse['data']);
        $this->assertIsArray($callHostingPlanResponse['data']['hostingPlans']);
        $this->assertNotEmpty($callHostingPlanResponse['data']['hostingPlans']);

        $hostingPlanIsFound = false;
        foreach ($callHostingPlanResponse['data']['hostingPlans'] as $hostingPlan) {
            if ($hostingPlan['id'] == $hostingPlanId) {
                $hostingPlanIsFound = true;
                break;
            }
        }
        $this->assertTrue($hostingPlanIsFound);

        $callCustomerStoreResponse = $this->callApiAuthorizedRouteAction('api.customers.store',[
            'name' => 'Phyre Unit Test Microweber Customer',
            'email' => 'phyre-unit-test-microweber-'.rand(1000, 9999).'@phyre.com',
        ])->json();
        $this->assertArrayHasKey('status', $callCustomerStoreResponse);
        $this->assertTrue($callCustomerStoreResponse['status'] == 'ok');
        $this->assertArrayHasKey('data', $callCustomerStoreResponse);
        $this->assertArrayHasKey('customer', $callCustomerStoreResponse['data']);
        $this->assertArrayHasKey('id', $callCustomerStoreResponse['data']['customer']);
        $this->assertIsInt($callCustomerStoreResponse['data']['customer']['id']);
        $customerId = $callCustomerStoreResponse['data']['customer']['id'];

        // TODO
        return;

        $hostingSubscriptionDomain = 'phyre-unit-test-microweber-'.rand(1000, 9999).'.com';
        $callHostingSubscriptionStoreResponse = $this->callApiAuthorizedRouteAction('api.hosting-subscriptions.store',[
            'customer_id' => $customerId,
            'hosting_plan_id' => $hostingPlanId,
            'domain' => $hostingSubscriptionDomain,
        ])->json();

        $apacheBuild = new ApacheBuild();
        $apacheBuild->handle();

        if (!isset($callHostingSubscriptionStoreResponse['status'])) {
            $this->fail(json_encode($callHostingSubscriptionStoreResponse));
        }

        $this->assertArrayHasKey('status', $callHostingSubscriptionStoreResponse);
        $this->assertTrue($callHostingSubscriptionStoreResponse['status'] == 'ok');
        $this->assertArrayHasKey('data', $callHostingSubscriptionStoreResponse);
        $this->assertArrayHasKey('hostingSubscription', $callHostingSubscriptionStoreResponse['data']);
        $this->assertArrayHasKey('id', $callHostingSubscriptionStoreResponse['data']['hostingSubscription']);
        $this->assertIsInt($callHostingSubscriptionStoreResponse['data']['hostingSubscription']['id']);
        $this->assertArrayHasKey('customer_id', $callHostingSubscriptionStoreResponse['data']['hostingSubscription']);
        $this->assertIsInt($callHostingSubscriptionStoreResponse['data']['hostingSubscription']['customer_id']);
        $this->assertTrue($callHostingSubscriptionStoreResponse['data']['hostingSubscription']['customer_id'] == $customerId);
        $this->assertArrayHasKey('hosting_plan_id', $callHostingSubscriptionStoreResponse['data']['hostingSubscription']);
        $this->assertIsInt($callHostingSubscriptionStoreResponse['data']['hostingSubscription']['hosting_plan_id']);


        // Check domain is accessible
//        shell_exec('sudo echo "0.0.0.0 '.$hostingSubscriptionDomain.'" | sudo tee -a /etc/hosts');
//
//        $domainAccess = shell_exec('curl -s -o /dev/null -w "%{http_code}" http://'.$hostingSubscriptionDomain);
//        $this->assertTrue($domainAccess == 200);
//
//        $indexPageContent = shell_exec('curl -s http://'.$hostingSubscriptionDomain);
//
//        $this->assertTrue(Str::contains($indexPageContent,'Microweber'));

    }
}
