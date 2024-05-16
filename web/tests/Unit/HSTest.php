<?php

namespace Tests\Unit;

use App\Http\Middleware\ApiKeyMiddleware;
use PHPUnit\Framework\TestCase;
use Tests\Feature\Api\ActionTestCase;

class HSTest extends ActionTestCase
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

    function testIndex()
    {
        // Make unauthorized call
        $callUnauthorizedResponse = $this->callRouteAction('api.hosting-subscriptions.index')->json();
        $this->assertArrayHasKey('error', $callUnauthorizedResponse);
        $this->assertTrue($callUnauthorizedResponse['error'] == 'Unauthorized');

        // Make authorized call
        $callResponse = $this->callApiAuthorizedRouteAction('api.hosting-subscriptions.index')->json();

        $this->assertArrayHasKey('data', $callResponse);
        $this->assertArrayHasKey('message', $callResponse);
        $this->assertArrayHasKey('status', $callResponse);
        $this->assertArrayHasKey('hostingSubscriptions', $callResponse['data']);
        $this->assertIsArray($callResponse['data']['hostingSubscriptions']);
        $this->assertIsString($callResponse['message']);
        $this->assertIsString($callResponse['status']);
        $this->assertTrue($callResponse['status'] == 'ok');

    }

}
