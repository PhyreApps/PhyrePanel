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

class PhyreApperance extends BaseSettings
{
    protected static bool $shouldRegisterNavigation = false;

    public function schema(): array|Closure
    {
        return [
            TextInput::make('general.brand_name'),
            TextInput::make('general.brand_logo_url'),
            ColorPicker::make('general.brand_primary_color'),
        ];
    }
}
