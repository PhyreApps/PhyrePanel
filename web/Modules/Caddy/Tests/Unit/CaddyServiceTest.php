<?php

namespace Modules\Caddy\Tests\Unit;

use Tests\TestCase;
use Modules\Caddy\App\Services\CaddyService;

class CaddyServiceTest extends TestCase
{
    protected CaddyService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CaddyService();
    }

    /** @test */
    public function it_initializes_with_correct_default_paths()
    {
        $reflection = new \ReflectionClass($this->service);
        
        $binaryPath = $reflection->getProperty('binaryPath');
        $binaryPath->setAccessible(true);
        
        $configPath = $reflection->getProperty('configPath');
        $configPath->setAccessible(true);
        
        $this->assertEquals('/usr/bin/caddy', $binaryPath->getValue($this->service));
        $this->assertEquals('/etc/caddy/Caddyfile', $configPath->getValue($this->service));
    }

    /** @test */
    public function is_running_returns_boolean()
    {
        $result = $this->service->isRunning();
        $this->assertIsBool($result);
    }

    /** @test */
    public function get_status_returns_array_with_required_keys()
    {
        $status = $this->service->getStatus();
        
        $this->assertIsArray($status);
        $this->assertArrayHasKey('running', $status);
        $this->assertArrayHasKey('pid', $status);
        $this->assertArrayHasKey('uptime', $status);
        $this->assertArrayHasKey('memory', $status);
        $this->assertArrayHasKey('version', $status);
        
        $this->assertIsBool($status['running']);
    }

    /** @test */
    public function validate_config_returns_proper_structure()
    {
        $result = $this->service->validateConfig();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('valid', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertIsBool($result['valid']);
        $this->assertIsString($result['message']);
    }

    /** @test */
    public function get_config_stats_returns_proper_structure()
    {
        $stats = $this->service->getConfigStats();
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('domains', $stats);
        $this->assertArrayHasKey('ssl_certs', $stats);
        $this->assertArrayHasKey('last_update', $stats);
        
        $this->assertIsInt($stats['domains']);
        $this->assertIsInt($stats['ssl_certs']);
    }

    /** @test */
    public function get_health_checks_returns_valid_structure()
    {
        $checks = $this->service->getHealthChecks();
        
        $this->assertIsArray($checks);
        $this->assertNotEmpty($checks);
        
        foreach ($checks as $check) {
            $this->assertArrayHasKey('name', $check);
            $this->assertArrayHasKey('description', $check);
            $this->assertArrayHasKey('status', $check);
            $this->assertArrayHasKey('last_checked', $check);
            
            $this->assertIsString($check['name']);
            $this->assertIsString($check['description']);
            $this->assertContains($check['status'], ['healthy', 'unhealthy', 'warning']);
            $this->assertIsString($check['last_checked']);
        }
    }

    /** @test */
    public function get_recent_activity_returns_array()
    {
        $activity = $this->service->getRecentActivity();
        
        $this->assertIsArray($activity);
        
        foreach ($activity as $entry) {
            $this->assertArrayHasKey('type', $entry);
            $this->assertArrayHasKey('message', $entry);
            $this->assertArrayHasKey('timestamp', $entry);
            
            $this->assertContains($entry['type'], ['info', 'success', 'warning', 'error']);
            $this->assertIsString($entry['message']);
        }
    }

    /** @test */
    public function service_management_methods_return_proper_structure()
    {
        $methods = ['start', 'stop', 'restart', 'reload'];
        
        foreach ($methods as $method) {
            $result = $this->service->$method();
            
            $this->assertIsArray($result);
            $this->assertArrayHasKey('success', $result);
            $this->assertArrayHasKey('message', $result);
            $this->assertIsBool($result['success']);
            $this->assertIsString($result['message']);
        }
    }

    /** @test */
    public function get_version_returns_string_or_null()
    {
        $version = $this->service->getVersion();
        $this->assertTrue(is_string($version) || is_null($version));
    }
}
