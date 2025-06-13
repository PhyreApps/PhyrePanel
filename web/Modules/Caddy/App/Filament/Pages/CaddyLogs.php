<?php

namespace Modules\Caddy\App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Modules\Caddy\App\Filament\Clusters\Caddy;

class CaddyLogs extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'caddy::filament.pages.caddy-logs';

    protected static ?string $cluster = Caddy::class;

    protected static ?string $navigationGroup = 'Caddy';

    protected static ?string $navigationLabel = 'Logs';

    protected static ?int $navigationSort = 3;

    public $logs = '';
    public $accessLogs = '';
    public $errorLogs = '';

    public function mount(): void
    {
        $this->loadLogs();
    }

    public function loadLogs(): void
    {
        // Load Caddy system logs
        $this->logs = $this->getSystemLogs();
        
        // Load access logs if available
        $this->accessLogs = $this->getAccessLogs();
        
        // Load error logs if available
        $this->errorLogs = $this->getErrorLogs();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh Logs')
                ->icon('heroicon-o-arrow-path')
                ->action(fn() => $this->loadLogs()),

            Action::make('clearLogs')
                ->label('Clear Logs')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    shell_exec('sudo truncate -s 0 /var/log/caddy/access.log');
                    shell_exec('sudo truncate -s 0 /var/log/caddy/error.log');
                    shell_exec('sudo journalctl --vacuum-time=1s --unit=caddy');
                    $this->loadLogs();
                }),
        ];
    }

    private function getSystemLogs(): string
    {
        $output = shell_exec('journalctl -u caddy --no-pager -n 100 2>/dev/null');
        return $output ?: 'No system logs available';
    }

    private function getAccessLogs(): string
    {
        if (file_exists('/var/log/caddy/access.log')) {
            $output = shell_exec('tail -n 100 /var/log/caddy/access.log 2>/dev/null');
            return $output ?: 'No access logs available';
        }
        return 'Access log file not found';
    }

    private function getErrorLogs(): string
    {
        if (file_exists('/var/log/caddy/error.log')) {
            $output = shell_exec('tail -n 100 /var/log/caddy/error.log 2>/dev/null');
            return $output ?: 'No error logs available';
        }
        return 'Error log file not found';
    }

    public function getTitle(): string
    {
        return 'Caddy Logs';
    }
}
