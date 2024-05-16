<?php

namespace Tests\Unit;

use App\Http\Middleware\ApiKeyMiddleware;
use App\Installers\Server\Applications\NodeJsInstaller;
use App\Installers\Server\Applications\PHPInstaller;
use App\Installers\Server\Applications\PythonInstaller;
use App\Jobs\ApacheBuild;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Domain;
use App\Models\HostingPlan;
use App\SupportedApplicationTypes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use Tests\Feature\Api\ActionTestCase;

class HSNodeJSTest extends ActionTestCase
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

        $this->assertRouteContainsMiddleware(
            'api.hosting-subscriptions.destroy',
            ApiKeyMiddleware::class
        );

    }

    function testCreate()
    {

        $this->assertTrue(Str::contains(php_uname(),'Ubuntu'));

        $isNodeJsInstalled = false;

        if (!$isNodeJsInstalled) {
            // Make Apache+NodeJS Application Server with all supported php versions and modules
            $installLogFilePath = storage_path('install-apache-nodejs-log-unit-test.txt');
            if (is_file($installLogFilePath)) {
                unlink($installLogFilePath);
            }

            $nodeJSInstaller = new NodeJsInstaller();
            $nodeJSInstaller->setNodeJsVersions(array_keys(SupportedApplicationTypes::getNodeJsVersions()));
            $nodeJSInstaller->setLogFilePath($installLogFilePath);
            $nodeJSInstaller->install();

            $installationSuccess = false;
            for ($i = 1; $i <= 100; $i++) {
                $logContent = file_get_contents($installLogFilePath);
                if (str_contains($logContent, 'All packages installed successfully!')) {
                    $installationSuccess = true;
                    break;
                }
                sleep(3);
            }

            if (!$installationSuccess) {
                $logContent = file_get_contents($installLogFilePath);
                $this->fail('Apache+NodeJS installation failed. Log: ' . $logContent);
            }

            $this->assertTrue($installationSuccess, 'Apache+NodeJS installation failed');
        }

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
        $randId = rand(10000, 99999);
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
        $randId = rand(10000, 99999);
        $hostingPlanId = null;

        $createHostingPlan = new HostingPlan();
        $createHostingPlan->name = 'Phyre Unit Test #'.$randId;
        $createHostingPlan->description = 'Unit Test Hosting Plan';
        $createHostingPlan->default_server_application_type = 'apache_nodejs';
        $createHostingPlan->default_server_application_settings = [
            'nodejs_version' => '20',
        ];
        $createHostingPlan->additional_services = [];
        $createHostingPlan->features = [];
        $createHostingPlan->limitations = [];
        $createHostingPlan->save();

        $hostingPlanId = $createHostingPlan->id;

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
