<?php

namespace App\Filament\Pages\Settings;

use App\Helpers;
use App\MasterDomain;
use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Storage;
use Monarobase\CountryList\CountryList;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;
use Symfony\Component\Console\Input\Input;

class Settings extends BaseSettings
{
    protected static ?string $navigationGroup = 'Server Management';

    protected static ?int $navigationSort = 4;

    public function save() : void
    {
        parent::save();

        // Make master domain virtual host
        $masterDomain = new MasterDomain();
        $masterDomain->configureVirtualHost();
    }

    public function schema(): array|Closure
    {

        return [
            Tabs::make('Settings')
                ->schema([
                    Tabs\Tab::make('General')
                        ->schema([
                            TextInput::make('general.brand_name'),
                            TextInput::make('general.brand_logo_url'),
                            ColorPicker::make('general.brand_primary_color'),

                            TextInput::make('general.master_domain')->live(),

                            TextInput::make('general.master_email'),
                            Select::make('general.master_country')
                                ->searchable()
                                ->options(function () {
                                    $countryList = new CountryList();
                                    return $countryList->getList();
                                }),
                            TextInput::make('general.master_locality'),
                            TextInput::make('general.organization_name'),
                        ]),
                    Tabs\Tab::make('Apache Web Pages')
                        ->schema([
                            Textarea::make('general.master_domain_page_html'),
                            Textarea::make('general.domain_suspend_page_html'),
                            Textarea::make('general.domain_created_page_html'),
                        ]),

                    Tabs\Tab::make('Backups')
                        ->schema([
                            TextInput::make('general.backup_path')
                                ->default(Storage::path('backups'))
                        ]),
                ]),
        ];
    }
}
