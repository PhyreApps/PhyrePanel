<?php

namespace Modules\Microweber\Filament\Clusters\Microweber\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Modules\Microweber\App\Models\MicroweberInstallation;
use Modules\Microweber\Filament\Clusters\MicroweberCluster;
use Modules\Microweber\Jobs\RunInstallationCommands;


class Utilities extends Page
{
    protected static ?string $navigationGroup = 'Microweber';

    protected static ?string $cluster = MicroweberCluster::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static string $view = 'microweber::filament.admin.pages.utilities';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Utilities';

    public static function getNavigationLabel(): string
    {
        return self::$navigationLabel;
    }

    public function getTitle(): string
    {
        return self::$navigationLabel;
    }

    protected function getViewData(): array
    {
        $installations = MicroweberInstallation::with('domain')->get();

        return [
            'installations' => $installations,
            'totalInstallations' => $installations->count(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('clearCacheAll')
                ->label('Clear Cache (All Installations)')
                ->icon('heroicon-o-trash')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Clear Cache for All Installations')
                ->modalDescription('This will run "php artisan cache:clear" on all Microweber installations. This may take some time.')
                ->action(function () {
                    RunInstallationCommands::dispatch('cache:clear');

                    Notification::make()
                        ->title('Cache Clear Job Dispatched')
                        ->body('The cache clear command has been queued for all installations.')
                        ->success()
                        ->send();
                }),

            Action::make('composerPublicshAssets')
                ->label('Composer Publish Assets (All Installations)')
                ->icon('heroicon-o-code-bracket')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Composer Publish Assets for All Installations')
                ->modalDescription('This will run "composer:publish-assets" on all Microweber installations. This may take some time.')
                ->action(function () {
                    RunInstallationCommands::dispatch('composer:publish-assets');

                    Notification::make()
                        ->title('Composer composer:publish-assets Job Dispatched')
                        ->body('The composer:publish-assets command has been queued for all installations.')
                        ->success()
                        ->send();
                }),


            Action::make('composerDumpAll')
                ->label('Composer Dump (All Installations)')
                ->icon('heroicon-o-code-bracket')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Composer Dump for All Installations')
                ->modalDescription('This will run "composer dump-autoload" on all Microweber installations. This may take some time.')
                ->action(function () {
                    RunInstallationCommands::dispatch('composer:dump');

                    Notification::make()
                        ->title('Composer Dump Job Dispatched')
                        ->body('The composer dump command has been queued for all installations.')
                        ->success()
                        ->send();
                }),

            Action::make('microweberVendorAssetsSymlink')
                ->label('Microweber Vendor Assets Symlink (All Installations)')
                ->icon('heroicon-o-code-bracket')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Microweber Vendor Assets Symlink for All Installations')
                ->modalDescription('This will run "microweber:vendor-assets-symlink" on all Microweber installations. This may take some time.')
                ->action(function () {
                    RunInstallationCommands::dispatchSync('microweber:vendor-assets-symlink');

                    Notification::make()
                        ->title('Microweber Vendor Assets Symlink Job Dispatched')
                        ->body('The microweber:vendor-assets-symlink command has been queued for all installations.')
                        ->success()
                        ->send();
                }),
            Action::make('microweberPostUpdate')
                ->label('Microweber reload modules')
                ->icon('heroicon-o-code-bracket')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Microweber Reload Modules for All Installations')
                ->modalDescription('This will run "microweber:reload-database" on all Microweber installations. This may take some time.')
                ->action(function () {
                    RunInstallationCommands::dispatchSync('microweber:reload-database');

                    Notification::make()
                        ->title('Microweber Reload Database Job Dispatched')
                        ->body('The microweber:reload-database command has been queued for all installations.')
                        ->success()
                        ->send();
                }),

            Action::make('customCommand')
                ->label('Run Custom Command')

                ->color('gray')
                ->form([
                    TextInput::make('command')
                        ->label('Command')
                        ->helperText('Enter the command to run (e.g., "cache:clear", "config:cache", etc.)')
                        ->required(),
                ])
                ->action(function (array $data) {
                    RunInstallationCommands::dispatch($data['command']);

                    Notification::make()
                        ->title('Custom Command Job Dispatched')
                        ->body("The command '{$data['command']}' has been queued for all installations.")
                        ->success()
                        ->send();
                }),
        ];
    }

    public function clearCacheForInstallation($installationId)
    {
        RunInstallationCommands::dispatch('cache:clear', $installationId);

        Notification::make()
            ->title('Cache Clear Job Dispatched')
            ->body('The cache clear command has been queued for this installation.')
            ->success()
            ->send();
    }

    public function composerDumpForInstallation($installationId)
    {
        RunInstallationCommands::dispatch('composer:dump', $installationId);

        Notification::make()
            ->title('Composer Dump Job Dispatched')
            ->body('The composer dump command has been queued for this installation.')
            ->success()
            ->send();
    }
}
