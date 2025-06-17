<?php

namespace Modules\Microweber\Filament\Clusters\Microweber\Pages;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Artisan;
use MicroweberPackages\ComposerClient\Client;
use Modules\Microweber\Filament\Clusters\MicroweberCluster;
use Modules\Microweber\Jobs\DownloadMicroweber;
use Modules\Microweber\Jobs\UpdateWhitelabelToWebsites;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;

class EnvVars extends BaseSettings
{
    protected static ?string $navigationGroup = 'Microweber';

    protected static ?string $cluster = MicroweberCluster::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Env variables';

    public static function getNavigationLabel(): string
    {
        return self::$navigationLabel;
    }

    public function getTitle(): string
    {
        return self::$navigationLabel;
    }

    public function save(): void
    {
        parent::save();

        // Dispatch job to update env vars across all websites
        dispatch_sync(new \Modules\Microweber\Jobs\UpdateEnvVarsToWebsites());
    }

    public function schema(): array
    {
        return [
            Section::make('Environment Variables')
                ->schema([


                    Textarea::make('microweber.env_vars.custom_env')
                        ->label('Custom Environment Variables')
                        ->helperText('Enter additional environment variables in KEY=VALUE format, one per line'),
                ])
        ];
    }
}
