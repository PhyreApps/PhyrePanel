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
        $isValid = strpos($output, 'valid') !== false;
        
        return [
            'status' => $isValid ? 'healthy' : 'unhealthy',
            'message' => $isValid ? 'Configuration is valid' : 'Configuration has errors',
            'details' => $output,
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

    public function getTitle(): string
    {
        return 'Caddy Health Check';
    }
}
