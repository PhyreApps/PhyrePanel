<?php

namespace Modules\Caddy\App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Modules\Caddy\App\Filament\Clusters\Caddy;
use Modules\Caddy\App\Services\CaddyService;

class CaddyDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string $view = 'caddy::filament.pages.caddy-dashboard';

    protected static ?string $cluster = Caddy::class;

    protected static ?string $navigationGroup = 'Caddy';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 0;

    protected CaddyService $caddyService;

    public function boot(): void
    {
        $this->caddyService = app(CaddyService::class);
    }

    public function getServiceStatusProperty(): array
    {
        return $this->caddyService->getStatus();
    }

    public function getCaddyVersionProperty(): ?string
    {
        return $this->caddyService->getVersion();
    }

    public function getConfigStatusProperty(): array
    {
        return $this->caddyService->validateConfig();
    }

    public function getConfigStatsProperty(): array
    {
        return $this->caddyService->getConfigStats();
    }

    public function getHealthChecksProperty(): array
    {
        return $this->caddyService->getHealthChecks();
    }

    public function getRecentActivityProperty(): array
    {
        return $this->caddyService->getRecentActivity();
    }

    public function startService(): void
    {
        $result = $this->caddyService->start();
        
        if ($result['success']) {
            Notification::make()
                ->title('Service Started')
                ->body($result['message'])
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to Start Service')
                ->body($result['message'])
                ->danger()
                ->send();
        }
    }

    public function stopService(): void
    {
        $result = $this->caddyService->stop();
        
        if ($result['success']) {
            Notification::make()
                ->title('Service Stopped')
                ->body($result['message'])
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to Stop Service')
                ->body($result['message'])
                ->danger()
                ->send();
        }
    }

    public function restartService(): void
    {
        $result = $this->caddyService->restart();
        
        if ($result['success']) {
            Notification::make()
                ->title('Service Restarted')
                ->body($result['message'])
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to Restart Service')
                ->body($result['message'])
                ->danger()
                ->send();
        }
    }

    public function reloadConfig(): void
    {
        $result = $this->caddyService->reload();
        
        if ($result['success']) {
            Notification::make()
                ->title('Configuration Reloaded')
                ->body($result['message'])
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Failed to Reload Configuration')
                ->body($result['message'])
                ->danger()
                ->send();
        }
    }

    public function validateConfig(): void
    {
        $result = $this->caddyService->validateConfig();
        
        if ($result['valid']) {
            Notification::make()
                ->title('Configuration Valid')
                ->body($result['message'])
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Configuration Invalid')
                ->body($result['message'])
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label('Refresh Status')
                ->icon('heroicon-o-arrow-path')
                ->action(fn() => $this->dispatch('$refresh')),
        ];
    }

    public function getTitle(): string
    {
        return 'Caddy Dashboard';
    }
}
