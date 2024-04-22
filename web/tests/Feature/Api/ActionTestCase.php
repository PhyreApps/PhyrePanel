<?php

namespace Tests\Feature\Api;

use App\User;
use Illuminate\Routing\Route;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

abstract class ActionTestCase extends TestCase
{
    use ApiKeysTrait;

    public function assertRouteContainsMiddleware($routeName, ...$names)
    {
        $route = $this->getRouteByName($routeName);

        foreach ($names as $name) {
            $this->assertContains(
                $name, $route->middleware(), "Route doesn't contain middleware [{$name}]"
            );
        }

        return $this;
    }

    public function assertRouteHasExactMiddleware($routeName, ...$names)
    {
        $route = $this->getRouteByName($routeName);

        $this->assertRouteContainsMiddleware(...$names);
        $this->assertTrue(count($names) === count($route->middleware()), 'Route contains not the same amount of middleware.');

        return $this;
    }


    /**
     * @return Route
     */
    private function getRouteByName($name): Route
    {
        $routes = \Illuminate\Support\Facades\Route::getRoutes();

        /** @var Route $route */
        $route = $routes->getByName($name);

        if (!$route) {
            $this->fail("Route with name [{$name}] not found!");
        }

        return $route;
    }

    /**
     * Call an unauthorized request to the controller
     *
     * @param array $data Request body
     * @param array $parameters Route parameters
     * @param array $headers Request headers
     *
     * @return TestResponse
     */
    protected function callRouteAction($routeName, array $data = [], array $parameters = [], array $headers = []): TestResponse
    {
        $route = $this->getRouteByName($routeName);
        $method = $route->methods()[0];
        $url = route($routeName, $parameters);

        return $this->json($method, $url, $data, $headers);
    }

    /**
     * Call an api authorized request to the controller
     *
     * @param array $data Request body
     * @param array $parameters Route parameters
     * @param array $headers Request headers
     *
     * @return TestResponse
     */
    protected function callApiAuthorizedRouteAction($routeName, array $data = [], array $parameters = [], array $headers = []): TestResponse
    {
        $route = $this->getRouteByName($routeName);
        $method = $route->methods()[0];
        $url = route($routeName, $parameters);

        $apiKey = $this->getApiKey();

        $headers['X-Api-Key'] = $apiKey['api_key'];
        $headers['X-Api-Secret'] = $apiKey['api_secret'];

        return $this->json($method, $url, $data, $headers);
    }

    /**
     *
     * Call an authorized request from random user to the controller
     *
     * @param array $data Request body
     * @param array $parameters Route parameters
     * @param array $headers Request headers
     * @param array $scopes
     *
     * @return TestResponse
     */
    protected function callAuthorizedRouteAction($routeName, array $data = [], array $parameters = [], array $headers = [], array $scopes = []): TestResponse
    {
        $user = factory(User::class)->create();

        return $this->callAuthorizedByUserRouteAction($user, $data, $parameters, $headers, $scopes);
    }

    /**
     * Call an authorized request from given user to the controller
     *
     * @param User $user
     * @param array $data
     * @param array $parameters
     * @param array $headers
     * @param array $scopes
     *
     * @return TestResponse
     */
    protected function callAuthorizedByUserRouteAction($routeName, User $user, array $data = [], array $parameters = [], array $headers = [], array $scopes = []): TestResponse
    {
        $this->signIn($user, [], $scopes);

        return $this->callRouteAction($data, $parameters, $headers);
    }
}
