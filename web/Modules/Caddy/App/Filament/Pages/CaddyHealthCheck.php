<?php

namespace Modules\Caddy\App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Modules\Caddy\App\Filament\Clusters\Caddy;

class CaddyHealthCheck extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static string $view = 'caddy::filament.pages.caddy-health-check';

    protected static ?string $cluster = Caddy::class;

    protected static ?string $navigationGroup = 'Caddy';

    protected static ?string $navigationLabel = 'Health Check';

    protected static ?int $navigationSort = 4;

    public $healthData = [];

    public function mount(): void
    {
        $this->runHealthCheck();
    }

    public function runHealthCheck(): void
    {
        $this->healthData = [
            'service_status' => $this->checkServiceStatus(),
            'config_validity' => $this->checkConfigValidity(),
            'port_availability' => $this->checkPortAvailability(),
            'ssl_certificates' => $this->checkSSLCertificates(),
            'apache_connectivity' => $this->checkApacheConnectivity(),
            'disk_space' => $this->checkDiskSpace(),
            'log_errors' => $this->checkLogErrors(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Run Health Check')
                ->icon('heroicon-o-arrow-path')
                ->action(fn() => $this->runHealthCheck()),
        ];
    }

    private function checkServiceStatus(): array
    {
        $status = shell_exec('systemctl is-active caddy');
        $isActive = trim($status) === 'active';
        
        return [
            'status' => $isActive ? 'healthy' : 'unhealthy',
            'message' => $isActive ? 'Caddy service is running' : 'Caddy service is not running',
            'details' => $status,
        ];
    }

    private function checkConfigValidity(): array
    {
        $output = shell_exec('caddy validate --config /etc/caddy/Caddyfile 2>&1');
        
        // Check if output contains success indicators (case insensitive)
        $outputLower = strtolower($output);
        $isValid = (strpos($outputLower, 'valid configuration') !== false) || 
                   (strpos($outputLower, 'configuration is valid') !== false) ||
                   (strpos($outputLower, 'valid') !== false && strpos($outputLower, 'error') === false);
        
        return [
            'status' => $isValid ? 'healthy' : 'unhealthy',
            'message' => $isValid ? 'Configuration is valid' : 'Configuration has errors',
            'details' => $output ?: 'No output from validation command',
        ];
    }

    private function checkPortAvailability(): array
    {
        $httpPort = setting('caddy.http_port', '80');
        $httpsPort = setting('caddy.https_port', '443');
        
        $httpCheck = shell_exec("netstat -tlnp | grep :$httpPort");
        $httpsCheck = shell_exec("netstat -tlnp | grep :$httpsPort");
        
        $httpOk = !empty($httpCheck) && strpos($httpCheck, 'caddy') !== false;
        $httpsOk = !empty($httpsCheck) && strpos($httpsCheck, 'caddy') !== false;
        
        return [
            'status' => ($httpOk && $httpsOk) ? 'healthy' : 'warning',
            'message' => "HTTP Port $httpPort: " . ($httpOk ? 'OK' : 'NOT OK') . ", HTTPS Port $httpsPort: " . ($httpsOk ? 'OK' : 'NOT OK'),
            'details' => [
                'http' => $httpCheck,
                'https' => $httpsCheck,
            ],
        ];
    }

    private function checkSSLCertificates(): array
    {
        $certCount = \App\Models\DomainSslCertificate::count();
        $domainCount = \App\Models\Domain::where('status', '!=', 'broken')->count();
        
        return [
            'status' => $certCount > 0 ? 'healthy' : 'warning',
            'message' => "SSL certificates available: $certCount for $domainCount domains",
            'details' => [
                'certificates' => $certCount,
                'domains' => $domainCount,
            ],
        ];
    }

    private function checkApacheConnectivity(): array
    {
        $apachePort = setting('caddy.apache_proxy_port', '8080');
        $response = @file_get_contents("http://127.0.0.1:$apachePort", false, stream_context_create([
            'http' => ['timeout' => 5]
        ]));
        
        return [
            'status' => $response !== false ? 'healthy' : 'unhealthy',
            'message' => $response !== false ? "Apache is reachable on port $apachePort" : "Cannot connect to Apache on port $apachePort",
            'details' => $response !== false ? 'Connection successful' : 'Connection failed',
        ];
    }

    private function checkDiskSpace(): array
    {
        $usage = shell_exec("df /var/lib/caddy | tail -1 | awk '{print $5}' | sed 's/%//'");
        $usage = intval(trim($usage));
        
        return [
            'status' => $usage < 90 ? 'healthy' : 'warning',
            'message' => "Disk usage: {$usage}%",
            'details' => $usage,
        ];
    }

    private function checkLogErrors(): array
    {
        $errorCount = 0;
        if (file_exists('/var/log/caddy/error.log')) {
            $errors = shell_exec('tail -n 100 /var/log/caddy/error.log | grep -i error | wc -l');
            $errorCount = intval(trim($errors));
        }
        
        return [
            'status' => $errorCount === 0 ? 'healthy' : 'warning',
            'message' => "Recent errors in logs: $errorCount",
            'details' => $errorCount,
        ];
    }

    public function refreshHealth(): void
    {
        $this->runHealthCheck();
    }

    public function getHealthResults(): array
    {
        // Calculate overall health status and score
        $healthyCount = 0;
        $warningCount = 0;
        $unhealthyCount = 0;
        $totalChecks = count($this->healthData);

        foreach ($this->healthData as $check) {
            switch ($check['status']) {
                case 'healthy':
                    $healthyCount++;
                    break;
                case 'warning':
                    $warningCount++;
                    break;
                case 'unhealthy':
                    $unhealthyCount++;
                    break;
            }
        }

        // Calculate overall score (healthy = 100%, warning = 50%, unhealthy = 0%)
        $score = $totalChecks > 0 ? round(($healthyCount * 100 + $warningCount * 50) / $totalChecks) : 0;

        // Determine overall status
        $overallStatus = 'healthy';
        if ($unhealthyCount > 0) {
            $overallStatus = 'unhealthy';
        } elseif ($warningCount > 0) {
            $overallStatus = 'warning';
        }

        return [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'overall' => [
                'status' => $overallStatus,
                'score' => $score,
                'healthy_count' => $healthyCount,
                'warning_count' => $warningCount,
                'unhealthy_count' => $unhealthyCount,
                'total_checks' => $totalChecks,
            ],
            'summary' => [
                'healthy' => $healthyCount,
                'warning' => $warningCount,
                'critical' => $unhealthyCount,
                'total' => $totalChecks,
            ],
            'checks' => [
                'service' => [
                    [
                        'name' => 'Caddy Service Status',
                        'status' => $this->healthData['service_status']['status'],
                        'message' => $this->healthData['service_status']['message'],
                        'details' => $this->healthData['service_status']['details'],
                    ],
                ],
                'configuration' => [
                    [
                        'name' => 'Configuration Validity',
                        'status' => $this->healthData['config_validity']['status'],
                        'message' => $this->healthData['config_validity']['message'],
                        'details' => $this->healthData['config_validity']['details'],
                    ],
                ],
                'network' => [
                    [
                        'name' => 'Port Availability',
                        'status' => $this->healthData['port_availability']['status'],
                        'message' => $this->healthData['port_availability']['message'],
                        'details' => $this->healthData['port_availability']['details'],
                    ],
                    [
                        'name' => 'Apache Connectivity',
                        'status' => $this->healthData['apache_connectivity']['status'],
                        'message' => $this->healthData['apache_connectivity']['message'],
                        'details' => $this->healthData['apache_connectivity']['details'],
                    ],
                ],
                'system' => [
                    [
                        'name' => 'SSL Certificates',
                        'status' => $this->healthData['ssl_certificates']['status'],
                        'message' => $this->healthData['ssl_certificates']['message'],
                        'details' => $this->healthData['ssl_certificates']['details'],
                    ],
                    [
                        'name' => 'Disk Space',
                        'status' => $this->healthData['disk_space']['status'],
                        'message' => $this->healthData['disk_space']['message'],
                        'details' => $this->healthData['disk_space']['details'],
                    ],
                    [
                        'name' => 'Log Errors',
                        'status' => $this->healthData['log_errors']['status'],
                        'message' => $this->healthData['log_errors']['message'],
                        'details' => $this->healthData['log_errors']['details'],
                    ],
                ],
            ],
        ];
    }

    public function getTitle(): string
    {
        return 'Caddy Health Check';
    }
}
