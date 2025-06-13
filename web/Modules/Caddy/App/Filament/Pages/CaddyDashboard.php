<?php

namespace Modules\Caddy\App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Modules\Caddy\App\Filament\Clusters\Caddy;

class CaddyDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string $view = 'caddy::filament.pages.caddy-dashboard';

    protected static ?string $cluster = Caddy::class;

    protected static ?string $navigationGroup = 'Caddy';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 0;

    public $isRunning = false;
    public $version = '';
    public $configValid = false;
    public $domainsCount = 0;
    public $sslCertificatesCount = 0;
    public $uptime = '';

    public function mount(): void
    {
        $this->loadStatus();
    }

    public function loadStatus(): void
    {
        // Check if Caddy is running
        $status = shell_exec('systemctl is-active caddy');
        $this->isRunning = trim($status) === 'active';

        // Get Caddy version
        $versionOutput = shell_exec('caddy version 2>/dev/null');
        $this->version = trim($versionOutput) ?: 'Unknown';

        // Check config validity
        $configCheck = shell_exec('caddy validate --config /etc/caddy/Caddyfile 2>&1');
        $this->configValid = strpos($configCheck, 'valid') !== false;

        // Count domains and SSL certificates
        $this->domainsCount = \App\Models\Domain::where('status', '!=', 'broken')->count();
        $this->sslCertificatesCount = \App\Models\DomainSslCertificate::count();

        // Get uptime
        if ($this->isRunning) {
            $uptimeOutput = shell_exec('systemctl show caddy --property=ActiveEnterTimestamp --value');
            if ($uptimeOutput) {
                $startTime = strtotime(trim($uptimeOutput));
                $uptime = time() - $startTime;
                $this->uptime = $this->formatUptime($uptime);
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh Status')
                ->icon('heroicon-o-arrow-path')
                ->action(fn() => $this->loadStatus()),
        ];
    }

    private function formatUptime(int $seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        $parts = [];
        if ($days > 0) $parts[] = "{$days}d";
        if ($hours > 0) $parts[] = "{$hours}h";
        if ($minutes > 0) $parts[] = "{$minutes}m";

        return implode(' ', $parts) ?: '< 1m';
    }

    public function getTitle(): string
    {
        return 'Caddy Dashboard';
    }
}
