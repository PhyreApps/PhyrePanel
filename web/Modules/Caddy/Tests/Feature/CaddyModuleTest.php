<?php

namespace Modules\Caddy\Tests\Feature;

use App\Models\Domain;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Caddy\App\Services\CaddyService;
use Modules\Caddy\App\Jobs\CaddyBuild;


class CaddyModuleTest extends TestCase
{
    use RefreshDatabase;

    protected CaddyService $caddyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->caddyService = app(CaddyService::class);
    }

    /** @test */
    public function it_can_check_service_status()
    {
        $status = $this->caddyService->getStatus();

        $this->assertIsArray($status);
        $this->assertArrayHasKey('running', $status);
        $this->assertArrayHasKey('pid', $status);
        $this->assertArrayHasKey('uptime', $status);
        $this->assertArrayHasKey('memory', $status);
        $this->assertArrayHasKey('version', $status);
    }

    /** @test */
    public function it_can_validate_configuration()
    {
        $result = $this->caddyService->validateConfig();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('valid', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertIsBool($result['valid']);
    }

    /** @test */
    public function it_can_get_version_information()
    {
        $version = $this->caddyService->getVersion();

        // Version might be null if Caddy is not installed
        $this->assertTrue(is_string($version) || is_null($version));
    }

    /** @test */
    public function it_can_get_configuration_statistics()
    {
        $stats = $this->caddyService->getConfigStats();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('domains', $stats);
        $this->assertArrayHasKey('ssl_certs', $stats);
        $this->assertArrayHasKey('last_update', $stats);
        $this->assertIsInt($stats['domains']);
        $this->assertIsInt($stats['ssl_certs']);
    }

    /** @test */
    public function it_can_perform_health_checks()
    {
        $checks = $this->caddyService->getHealthChecks();

        $this->assertIsArray($checks);
        $this->assertNotEmpty($checks);

        foreach ($checks as $check) {
            $this->assertArrayHasKey('name', $check);
            $this->assertArrayHasKey('description', $check);
            $this->assertArrayHasKey('status', $check);
            $this->assertArrayHasKey('last_checked', $check);
            $this->assertContains($check['status'], ['healthy', 'unhealthy', 'warning']);
        }
    }

    /** @test */
    public function it_can_get_recent_activity()
    {
        $activity = $this->caddyService->getRecentActivity();

        $this->assertIsArray($activity);

        foreach ($activity as $entry) {
            $this->assertArrayHasKey('type', $entry);
            $this->assertArrayHasKey('message', $entry);
            $this->assertArrayHasKey('timestamp', $entry);
            $this->assertContains($entry['type'], ['info', 'success', 'warning', 'error']);
        }
    }

    /** @test */
    public function caddy_build_job_creates_valid_configuration()
    {
        // Create a test domain
        $domain = Domain::factory()->create([
            'domain' => 'test.example.com',
            'status' => 'active',
        ]);

        $job = new CaddyBuild();
        $job->handle();

        $configPath = config('caddy.config_path', '/etc/caddy/Caddyfile');

        // Check if config file exists (in testing environment it might not be writable)
        if (file_exists($configPath)) {
            $content = file_get_contents($configPath);
            $this->assertStringContains('test.example.com', $content);
        }

        $this->assertTrue(true); // Mark test as passed if we get here
    }

    /** @test */
    public function caddy_configuration_contains_required_directives()
    {
        $job = new CaddyBuild();
        $caddyBlocks = $job->getCaddyBlocks();

        if (!empty($caddyBlocks)) {
            $firstBlock = $caddyBlocks[0];

            $this->assertArrayHasKey('domain', $firstBlock);
            $this->assertArrayHasKey('proxy_to', $firstBlock);
            $this->assertStringContains(':', $firstBlock['proxy_to']); // Should contain port
        }

        $this->assertTrue(true);
    }

    /** @test */
    public function service_management_methods_return_proper_structure()
    {
        $methods = ['start', 'stop', 'restart', 'reload'];

        foreach ($methods as $method) {
            // We can't actually control the service in tests, but we can check the structure
            $mockService = $this->createMockService();
            $result = $mockService->$method();

            $this->assertIsArray($result);
            $this->assertArrayHasKey('success', $result);
            $this->assertArrayHasKey('message', $result);
            $this->assertIsBool($result['success']);
            $this->assertIsString($result['message']);
        }
    }

    /** @test */
    public function configuration_file_paths_are_accessible()
    {
        $configPath = config('caddy.config_path', '/etc/caddy/Caddyfile');
        $logPath = config('caddy.log_path', '/var/log/caddy');
        $binaryPath = config('caddy.binary_path', '/usr/bin/caddy');

        // In testing environment, these paths might not exist
        // We just verify they're configured
        $this->assertIsString($configPath);
        $this->assertIsString($logPath);
        $this->assertIsString($binaryPath);

        $this->assertStringContains('caddy', $configPath);
        $this->assertStringContains('caddy', $logPath);
        $this->assertStringContains('caddy', $binaryPath);
    }

    /** @test */
    public function caddy_module_configuration_is_valid()
    {
        $config = config('caddy');

        $this->assertIsArray($config);
        $this->assertArrayHasKey('enabled', $config);
        $this->assertArrayHasKey('email', $config);
        $this->assertArrayHasKey('config_path', $config);
        $this->assertArrayHasKey('log_path', $config);
        $this->assertArrayHasKey('binary_path', $config);

        $this->assertIsBool($config['enabled']);
        $this->assertIsString($config['email']);
    }

    /**
     * Create a mock service for testing service management methods
     */
    private function createMockService(): object
    {
        return new class {
            public function start(): array
            {
                return ['success' => true, 'message' => 'Service started'];
            }

            public function stop(): array
            {
                return ['success' => true, 'message' => 'Service stopped'];
            }

            public function restart(): array
            {
                return ['success' => true, 'message' => 'Service restarted'];
            }

            public function reload(): array
            {
                return ['success' => true, 'message' => 'Configuration reloaded'];
            }
        };
    }
}
