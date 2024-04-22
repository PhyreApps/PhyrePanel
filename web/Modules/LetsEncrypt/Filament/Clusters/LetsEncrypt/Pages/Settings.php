<?php

namespace Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Modules\LetsEncrypt\Filament\Clusters\LetsEncryptCluster;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;

class Settings extends BaseSettings
{
    protected static ?string $navigationGroup = 'Let\'s Encrypt';

    protected static ?string $cluster = LetsEncryptCluster::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?int $navigationSort = 3;

    public function schema(): array
    {
        return [

            Section::make('Settings')
                ->schema([

                    Toggle::make('install_certificate_on_new_domain')
                        ->label('Install Certificate on New Created Domains'),

                ]),
        ];
    }
}
