<?php

namespace Modules\Caddy\App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Modules\Caddy\App\Filament\Clusters\Caddy;
use Modules\Caddy\App\Jobs\CaddyBuild;

class CaddyManagement extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'caddy::filament.pages.caddy-management';

    protected static ?string $cluster = Caddy::class;

    protected static ?string $navigationGroup = 'Caddy';

    protected static ?string $navigationLabel = 'Management';

    protected static ?int $navigationSort = 2;

    public $caddyfileContent = '';
    public $customConfig = '';

    public function mount(): void
    {
        $this->loadCaddyfile();
    }

    public function loadCaddyfile(): void
    {
        if (file_exists('/etc/caddy/Caddyfile')) {
            $this->caddyfileContent = file_get_contents('/etc/caddy/Caddyfile');
        } else {
            $this->caddyfileContent = 'Caddyfile not found';
        }
    }    protected function getHeaderActions(): array
    {
        return [
            Action::make('reloadConfig')
                ->label('Reload Configuration')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    shell_exec('systemctl reload caddy');
                    Notification::make()
                        ->title('Caddy configuration reloaded')
                        ->success()
                        ->send();
                }),

            Action::make('restartService')
                ->label('Restart Service')
                ->icon('heroicon-o-power')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    shell_exec('systemctl restart caddy');
                    Notification::make()
                        ->title('Caddy service restarted')
                        ->success()
                        ->send();
                }),

            Action::make('formatConfig')
                ->label('Format Configuration')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->action(function () {
                    $this->formatCaddyfile();
                }),

            Action::make('validateConfig')
                ->label('Validate Configuration')
                ->icon('heroicon-o-check-circle')
                ->color('gray')
                ->action(function () {
                    $output = shell_exec('caddy validate --config /etc/caddy/Caddyfile 2>&1');
                    $isValid = strpos($output, 'valid') !== false;

                    Notification::make()
                        ->title('Configuration ' . ($isValid ? 'Valid' : 'Invalid'))
                        ->body($isValid ? 'Caddyfile syntax is correct' : $output)
                        ->color($isValid ? 'success' : 'danger')
                        ->send();
                }),

            Action::make('rebuildFromDomains')
                ->label('Rebuild from Domains')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->action(function () {
                    CaddyBuild::dispatchSync();
                    $this->loadCaddyfile();

                    Notification::make()
                        ->title('Caddyfile rebuilt from domains')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function saveCaddyfile(): void
    {
        file_put_contents('/etc/caddy/Caddyfile', $this->caddyfileContent);

        // Validate the configuration
        $output = shell_exec('caddy validate --config /etc/caddy/Caddyfile 2>&1');
        $isValid = strpos($output, 'valid') !== false;

        if ($isValid) {

            shell_exec('systemctl reload caddy');

            Notification::make()
                ->title('Caddyfile saved and reloaded')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Configuration Error')
                ->body('Caddyfile has syntax errors: ' . $output)
                ->danger()
                ->send();
        }
    }

    public function formatCaddyfile(): void
    {
        $caddyConfigPath = '/etc/caddy/Caddyfile';
        $caddyBinary = '/usr/bin/caddy';

        if (!file_exists($caddyConfigPath)) {
            Notification::make()
                ->title('Error')
                ->body('Caddyfile not found')
                ->danger()
                ->send();
            return;
        }

        if (!is_executable($caddyBinary)) {
            Notification::make()
                ->title('Error')
                ->body('Caddy binary not found')
                ->danger()
                ->send();
            return;
        }

        // Create backup before formatting
        $backupPath = $caddyConfigPath . '.backup.format.' . date('Y-m-d-H-i-s');
        if (!copy($caddyConfigPath, $backupPath)) {
            Notification::make()
                ->title('Error')
                ->body('Failed to create backup')
                ->danger()
                ->send();
            return;
        }

        // Format the Caddyfile
        $command = "{$caddyBinary} fmt --overwrite {$caddyConfigPath} 2>&1";
        $output = shell_exec($command);
        $exitCode = shell_exec("echo $?");

        if (trim($exitCode) === '0') {
            $this->loadCaddyfile(); // Reload the formatted content

            Notification::make()
                ->title('Caddyfile Formatted')
                ->body('Configuration formatted successfully')
                ->success()
                ->send();
        } else {
            // Restore backup on failure
            copy($backupPath, $caddyConfigPath);

            Notification::make()
                ->title('Format Failed')
                ->body('Failed to format Caddyfile: ' . $output)
                ->danger()
                ->send();
        }
    }

    public function getTitle(): string
    {

        return 'Caddy Management';
    }
}
