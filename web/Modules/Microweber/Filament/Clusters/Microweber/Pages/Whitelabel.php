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

class Whitelabel extends BaseSettings
{
    protected static ?string $navigationGroup = 'Microweber';

    protected static ?string $cluster = MicroweberCluster::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Whitelabel';

    public static function getNavigationLabel(): string
    {
        return self::$navigationLabel;
    }

    public function getTitle(): string
    {
        return self::$navigationLabel;
    }

    public function save() : void
    {
        parent::save();

        Artisan::call('microweber:reinstall-installations');
    }

    public function schema(): array
    {

        $whitelabelIsActive = false;
        $composerClientLicensed = new Client();
        $consume = $composerClientLicensed->consumeLicense(setting('whitelabel_license_key'));
        if (isset($consume['status']) && $consume['status'] == 'Active') {
            $whitelabelIsActive = true;
        }

        $whitelabelFields = [];
        if ($whitelabelIsActive) {

            $whitelabelFields = [
                TextInput::make('microweber.whitelabel.brand_name')
                    ->label('Brand Name'),

                TextInput::make('microweber.whitelabel.brand_favicon')
                    ->label('Brand Favicon'),

                TextInput::make('microweber.whitelabel.admin_logo_login_link')
                    ->label('Admin login - White Label URL?'),

                Checkbox::make('microweber.whitelabel.enable_support_links')
                    ->label('Enable support links?'),

                TextInput::make('microweber.whitelabel.custom_support_url')
                    ->label('Custom support URL'),

                Textarea::make('microweber.whitelabel.powered_by_text')
                    ->label('Enter "Powered by" text'),

                Checkbox::make('microweber.whitelabel.powered_by_link')
                    ->label('Hide "Powered by" link'),

                Checkbox::make('microweber.whitelabel.enable_service_links')
                    ->label('Enable service links'),

                TextInput::make('microweber.whitelabel.logo_admin')
                    ->label('Logo for Admin panel (size: 180x35px)'),

                TextInput::make('microweber.whitelabel.logo_live_edit')
                    ->label('Logo for Live-Edit toolbar (size: 50x50px)'),

                TextInput::make('microweber.whitelabel.logo_login')
                    ->label('Logo for Login screen (max width: 290px)'),

                Checkbox::make('microweber.whitelabel.disable_marketplace')
                    ->label('Disable Microweber Marketplace'),

                Textarea::make('microweber.whitelabel.marketplace_repositories_urls')
                    ->label('Marketplace repository urls'),

                Checkbox::make('microweber.whitelabel.disable_powered_by_link')
                    ->label('Disable Powered by link'),

                TextInput::make('microweber.whitelabel.external_login_server_button_text')
                    ->label('External Login Server Button Text'),

                Checkbox::make('microweber.whitelabel.external_login_server_enable')
                    ->label('Enable External Login Server'),

                Checkbox::make('microweber.whitelabel.enable_microweber_service_links')
                    ->label('Enable Microweber Service Links'),

                Textarea::make('microweber.whitelabel.admin_colors_sass')
                    ->label('Enter "Admin colors" sass')
            ];
        }

        return [
            Section::make('Whitelabel')
                ->schema([

                    TextInput::make('whitelabel_license_key')
                        ->label('License Key'),


                    ...$whitelabelFields

                ]),
        ];
    }
}
