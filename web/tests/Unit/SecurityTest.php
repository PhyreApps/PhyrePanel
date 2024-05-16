<?php

namespace Tests\Unit;

use App\Jobs\ApacheBuild;
use Faker\Factory;
use Tests\Feature\Api\ActionTestCase;

class SecurityTest extends ActionTestCase
{
    public function testSecurity()
    {
        $callHostingSubscriptionStoreResponse = $this->callApiAuthorizedRouteAction(
            'api.hosting-subscriptions.store',
            [
                'customer_id' => '34232432',
                'hosting_plan_id'=> '4443232',
                'domain' => 'broken-domain-name',
            ]
        )->json();

        $this->assertArrayHasKey('error', $callHostingSubscriptionStoreResponse);
        $this->assertArrayHasKey('message', $callHostingSubscriptionStoreResponse);
        $this->assertArrayHasKey('errors', $callHostingSubscriptionStoreResponse);
        $this->assertArrayHasKey('domain', $callHostingSubscriptionStoreResponse['errors']);
        $this->assertSame('The selected customer id is invalid.', $callHostingSubscriptionStoreResponse['message']);

        $this->assertSame('The selected hosting plan id is invalid.', $callHostingSubscriptionStoreResponse['errors']['hosting_plan_id'][0]);
        $this->assertSame('The domain field format is invalid.', $callHostingSubscriptionStoreResponse['errors']['domain'][0]);


        // Create a customer
        $faker = Factory::create();
        $randomName = $faker->firstName() . ' ' . $faker->lastName();
        $randomEmail = $faker->email();
        $callCustomerStoreResponse = $this->callApiAuthorizedRouteAction(
            'api.customers.store',
            [
                'name' => $randomName,
                'email' => $randomEmail,
            ]
        )->json();
        $this->assertArrayHasKey('status', $callCustomerStoreResponse);
        $this->assertTrue($callCustomerStoreResponse['status'] == 'ok');

        $this->assertArrayHasKey('message', $callCustomerStoreResponse);
        $this->assertArrayHasKey('data', $callCustomerStoreResponse);
        $this->assertArrayHasKey('customer', $callCustomerStoreResponse['data']);
        $this->assertArrayHasKey('id', $callCustomerStoreResponse['data']['customer']);
        $this->assertIsInt($callCustomerStoreResponse['data']['customer']['id']);
        $customerId = $callCustomerStoreResponse['data']['customer']['id'];

        // Create a hosting subscription
        $randId = rand(1000, 9999);
        $callHostingPlansResponse = $this->callApiAuthorizedRouteAction('api.hosting-plans.index')->json();
        $this->assertArrayHasKey('status', $callHostingPlansResponse);
        $this->assertTrue($callHostingPlansResponse['status'] == 'ok');
        $this->assertArrayHasKey('data', $callHostingPlansResponse);
        $this->assertArrayHasKey('hostingPlans', $callHostingPlansResponse['data']);
        $this->assertIsArray($callHostingPlansResponse['data']['hostingPlans']);
        $this->assertNotEmpty($callHostingPlansResponse['data']['hostingPlans']);
        $hostingPlanId = $callHostingPlansResponse['data']['hostingPlans'][0]['id'];
        $this->assertIsInt($hostingPlanId);

        $hostingSubscriptionDomain = 'phyre-unit-test-'.$randId.'.com';
        $callHostingSubscriptionStoreResponse = $this->callApiAuthorizedRouteAction(
            'api.hosting-subscriptions.store',
            [
                'customer_id' => $customerId,
                'hosting_plan_id'=> $hostingPlanId,
                'domain' => $hostingSubscriptionDomain,
            ]
        )->json();

        $this->assertArrayHasKey('status', $callHostingSubscriptionStoreResponse);
        $this->assertTrue($callHostingSubscriptionStoreResponse['status'] == 'ok');

        $hostingSubscription = $callHostingSubscriptionStoreResponse['data']['hostingSubscription'];

        // Check user home dir permissions
        $homeDir = '/home';
        $this->assertDirectoryExists($homeDir);
        $getHomeDirPermission = substr(sprintf('%o', fileperms($homeDir)), -4);
        $this->assertSame('0711', $getHomeDirPermission);

        $userHomeDir = '/home/' . $hostingSubscription['system_username'];
        $this->assertDirectoryExists($userHomeDir);
        $getUserHomeDirPermission = substr(sprintf('%o', fileperms($userHomeDir)), -4);
        $this->assertSame('0711', $getUserHomeDirPermission);
        // 0711 - is the correct permission for /home/$user directory, because it is a home directory and it should be accessible only by the user and root.

        // Check domain dir permissions
        $domainDir = '/home/' . $hostingSubscription['system_username'] . '/public_html';
        $this->assertDirectoryExists($domainDir);
        $getDomainDirPermission = substr(sprintf('%o', fileperms($domainDir)), -4);
        $this->assertSame('0775', $getDomainDirPermission);

        // Check domain dir file permissions
        $domainDirFile = '/home/' . $hostingSubscription['system_username'] . '/public_html/index.php';
        $this->assertFileExists($domainDirFile);
        $getDomainDirFilePermission = substr(sprintf('%o', fileperms($domainDirFile)), -4);
        $this->assertSame('0775', $getDomainDirFilePermission);

        // Create second hosting subscription
        $randId = rand(1000, 9999);
        $callHostingSubscriptionStoreResponse = $this->callApiAuthorizedRouteAction(
            'api.hosting-subscriptions.store',
            [
                'customer_id' => $customerId,
                'hosting_plan_id'=> $hostingPlanId,
                'domain' => 'phyre-unit-test-'.$randId.'.com',
            ]
        )->json();

        $apacheBuild = new ApacheBuild();
        $apacheBuild->handle();

        $this->assertArrayHasKey('status', $callHostingSubscriptionStoreResponse);
        $this->assertTrue($callHostingSubscriptionStoreResponse['status'] == 'ok');
        $secondHostingSubscription = $callHostingSubscriptionStoreResponse['data']['hostingSubscription'];

        // Try to open /home directory with linux user
        $output = shell_exec("sudo -H -u ".$hostingSubscription['system_username']." bash -c 'ls -la /home'");
        $this->assertSame($output, null);

        // Try to open /home/$user with linux user
        $output = shell_exec("sudo -H -u ".$hostingSubscription['system_username']." bash -c 'ls -la /home/".$hostingSubscription['system_username']."'");
        $this->assertTrue(str_contains($output, 'public_html'));
        $this->assertTrue(str_contains($output, $hostingSubscription['system_username']));

        // Try to open /home/$user directory with another linux user
        $output = shell_exec("sudo -H -u ".$secondHostingSubscription['system_username']." bash -c 'ls -la /home/".$hostingSubscription['system_username']."'");
        $this->assertSame($output, null);


    }
}
