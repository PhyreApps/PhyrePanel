<?php

namespace Tests\Unit;

use App\Http\Middleware\ApiKeyMiddleware;
use App\Installers\Server\Applications\PHPInstaller;
use App\Jobs\ApacheBuild;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Domain;
use App\SupportedApplicationTypes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use Tests\Feature\Api\ActionTestCase;

class HSCreateTest extends ActionTestCase
{
    function testRouteContainsMiddleware()
    {
        $this->assertRouteContainsMiddleware(
            'api.hosting-subscriptions.index',
            ApiKeyMiddleware::class
        );

        $this->assertRouteContainsMiddleware(
                    'api.hosting-subscriptions.store',
            ApiKeyMiddleware::class
        );

        $this->assertRouteContainsMiddleware(
            'api.hosting-subscriptions.update',
            ApiKeyMiddleware::class
        );

//        $this->assertRouteContainsMiddleware(
//            'api.hosting-subscriptions.destroy',
//            ApiKeyMiddleware::class
//        );

    }

    function test_create()
    {
        // Make unauthorized call
        $callUnauthorizedResponse = $this->callRouteAction('api.hosting-subscriptions.store')->json();
        $this->assertArrayHasKey('error', $callUnauthorizedResponse);
        $this->assertTrue($callUnauthorizedResponse['error'] == 'Unauthorized');

        // Make authorized call without required parameters
        $callStoreResponse = $this->callApiAuthorizedRouteAction('api.hosting-subscriptions.store')->json();

        $this->assertArrayHasKey('message', $callStoreResponse);
        $this->assertArrayHasKey('errors', $callStoreResponse);

        $this->assertIsString($callStoreResponse['message']);
        $this->assertIsArray($callStoreResponse['errors']);

        // Create a customer
        $randId = rand(1000, 9999);
        $callCustomerStoreResponse = $this->callApiAuthorizedRouteAction(
            'api.customers.store',
            [
                'name' => 'Phyre Unit Test #'.$randId,
                'email' => 'unit-test-'.$randId.'@phyre.com',
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

        $apacheBuild = new ApacheBuild();
        $apacheBuild->handle();

        $this->assertArrayHasKey('status', $callHostingSubscriptionStoreResponse);
        $this->assertTrue($callHostingSubscriptionStoreResponse['status'] == 'ok');

        $this->assertArrayHasKey('message', $callHostingSubscriptionStoreResponse);
        $this->assertArrayHasKey('data', $callHostingSubscriptionStoreResponse);
        $this->assertArrayHasKey('hostingSubscription', $callHostingSubscriptionStoreResponse['data']);

        $this->assertArrayHasKey('id', $callHostingSubscriptionStoreResponse['data']['hostingSubscription']);
        $this->assertIsInt($callHostingSubscriptionStoreResponse['data']['hostingSubscription']['id']);

        $this->assertArrayHasKey('customer_id', $callHostingSubscriptionStoreResponse['data']['hostingSubscription']);
        $this->assertIsInt($callHostingSubscriptionStoreResponse['data']['hostingSubscription']['customer_id']);
        $this->assertTrue($callHostingSubscriptionStoreResponse['data']['hostingSubscription']['customer_id'] == $customerId);

        $this->assertArrayHasKey('hosting_plan_id', $callHostingSubscriptionStoreResponse['data']['hostingSubscription']);
        $this->assertIsInt($callHostingSubscriptionStoreResponse['data']['hostingSubscription']['hosting_plan_id']);
        $this->assertTrue($callHostingSubscriptionStoreResponse['data']['hostingSubscription']['hosting_plan_id'] == $hostingPlanId);

        $this->assertArrayHasKey('domain', $callHostingSubscriptionStoreResponse['data']['hostingSubscription']);

        $this->assertIsString($callHostingSubscriptionStoreResponse['data']['hostingSubscription']['domain']);
        $this->assertTrue($callHostingSubscriptionStoreResponse['data']['hostingSubscription']['domain'] == $hostingSubscriptionDomain);

        $hostingSubscriptionData = $callHostingSubscriptionStoreResponse['data']['hostingSubscription'];

        // Get domain details
        $callDomainDetailsResponse = $this->callApiAuthorizedRouteAction(
            'api.domains.index',
            [
                'domain' => $hostingSubscriptionDomain,
            ]
        )->json();
        $callDomainDetailsResponseData = $callDomainDetailsResponse['data']['domains'];
        $this->assertIsArray($callDomainDetailsResponseData);
        $this->assertNotEmpty($callDomainDetailsResponseData);
        $this->assertArrayHasKey('id', $callDomainDetailsResponseData[0]);
        $this->assertArrayHasKey('domain', $callDomainDetailsResponseData[0]);
        $this->assertArrayHasKey('hosting_subscription_id', $callDomainDetailsResponseData[0]);
        $this->assertArrayHasKey('status', $callDomainDetailsResponseData[0]);
        $this->assertArrayHasKey('created_at', $callDomainDetailsResponseData[0]);
        $this->assertArrayHasKey('updated_at', $callDomainDetailsResponseData[0]);
        $this->assertTrue($callDomainDetailsResponseData[0]['domain'] == $hostingSubscriptionDomain);
        $this->assertTrue($callDomainDetailsResponseData[0]['hosting_subscription_id'] == $hostingSubscriptionData['id']);
        $this->assertTrue($callDomainDetailsResponseData[0]['status'] == Domain::STATUS_ACTIVE);
        $this->assertTrue($callDomainDetailsResponseData[0]['is_main'] == 1);
        $domainData = $callDomainDetailsResponseData[0];

        // Check virtual host is created
        $virtualHostFile = '/etc/apache2/apache2.conf';
        $this->assertFileExists($virtualHostFile);
        $virtualHostFileContent = file_get_contents($virtualHostFile);


        $this->assertStringContainsString('ServerName '.$hostingSubscriptionDomain, $virtualHostFileContent);
        //$this->assertStringContainsString('ServerAlias www.'.$hostingSubscriptionDomain, $virtualHostFileContent);

        $this->assertStringContainsString('Directory '.$domainData['domain_public'], $virtualHostFileContent);
        $this->assertStringContainsString('DocumentRoot '.$domainData['domain_public'], $virtualHostFileContent);
        $this->assertStringContainsString('php_admin_value open_basedir '.$domainData['home_root'], $virtualHostFileContent);

        // Check virtual host is enabled
        $this->assertFileExists('/etc/apache2/apache2.conf');

        // Check apache config is valid
        shell_exec('apachectl -t >> /tmp/apache_config_check.txt  2>&1');
        $apacheConfigTest = file_get_contents('/tmp/apache_config_check.txt');
        unlink('/tmp/apache_config_check.txt');

        $this->assertTrue(Str::contains($apacheConfigTest,'Syntax OK'));

        // Check domain is accessible
//        shell_exec('sudo echo "0.0.0.0 '.$hostingSubscriptionDomain.'" | sudo tee -a /etc/hosts');
//
//        $domainAccess = shell_exec('curl -s -o /dev/null -w "%{http_code}" http://'.$hostingSubscriptionDomain);
//        $this->assertTrue($domainAccess == 200);
//
//        $indexPageContent = shell_exec('curl -s http://'.$hostingSubscriptionDomain);
//
//        $this->assertTrue(Str::contains($indexPageContent,'Phyre Panel - PHP App'));


        // Check hosting subscription local database creation
        $newDatabase = new Database();
        $newDatabase->hosting_subscription_id = $hostingSubscriptionData['id'];
        $newDatabase->is_remote_database_server = 0;
        $newDatabase->database_name = 'ppdb'.$randId;
        $newDatabase->save();

        $newDatabaseUser = new DatabaseUser();
        $newDatabaseUser->database_id = $newDatabase->id;
        $newDatabaseUser->username = 'pput'.$randId;
        $newDatabaseUser->password = Str::password(24);
        $newDatabaseUser->save();
    }

}
