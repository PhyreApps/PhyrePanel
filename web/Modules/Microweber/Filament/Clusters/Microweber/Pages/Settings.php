<?php

namespace Modules\Microweber\Filament\Clusters\Microweber\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Modules\Microweber\Filament\Clusters\MicroweberCluster;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;

class Settings extends BaseSettings
{
    protected static ?string $navigationGroup = 'Microweber';

    protected static ?string $cluster = MicroweberCluster::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?int $navigationSort = 3;

    public function schema(): array
    {
        return [

            Section::make('Settings')
                ->schema([

                    Select::make('microweber.default_installation_template')
                        ->options([
                            'default' => 'Default',
                            'big' => 'BIG',
                        ])
                        ->label('Default Installation template'),

                    Select::make('microweber.default_installation_language')
                        ->options([
                            'en' => 'English',
                            'bg' => 'Bulgarian',
                        ])
                        ->label('Default Installation language'),

                    Select::make('microweber.default_installation_type')
                        ->options([
                            'sym-linked' => 'Sym-Linked (saves a big amount of disk space)',
                            'standalone' => 'Standalone',
                        ])
                        ->label('Default Installation type'),

                    Select::make('microweber.show_installing_app_notifications')
                        ->options([
                            'no' => 'No',
                            'yes' => 'Yes',
                        ])
                        ->label('Show installing app notifications'),

                    Select::make('microweber.instantly_install_ssl_certificate')
                        ->options([
                            'no' => 'No',
                            'yes' => 'Yes',
                        ])
                        ->label('Instantly install SSL certificate'),

                    Select::make('microweber.allow_customers_to_choose_installation_type')
                        ->options([
                            'yes' => 'Yes',
                            'no' => 'No',
                        ])
                        ->label('Allow customers to choose installation type'),

                    Select::make('microweber.allow_customers_to_choose_installation_database_driver')
                        ->options([
                            'yes' => 'Yes',
                            'no' => 'No',
                        ])
                        ->label('Allow customers to choose installation database driver'),

                    Select::make('microweber.database_driver')
                        ->options([
                            'mysql' => 'MySQL',
                            'sqlite' => 'SQLite',
                        ])
                        ->label('Database Driver'),

                    Select::make('microweber.update_app_channel')
                        ->options([
                            'stable' => 'Stable',
                            'beta' => 'Beta',
                            'development' => 'Development',
                        ])
                        ->label('Update App Channel'),

                    Select::make('microweber.update_app_automatically')
                        ->options([
                            'no' => 'No',
                            'yes' => 'Yes',
                        ])
                        ->label('Update App Automatically'),

                    Select::make('microweber.update_templates_automatically')
                        ->options([
                            'no' => 'No',
                            'yes' => 'Yes',
                        ])
                        ->label('Update Templates Automatically'),

                    Select::make('microweber.website_manager')
                        ->options([
                            'whmcs' => 'WHMCS',
                            'microweber_saas' => 'Microweber SAAS',
                        ])
                        ->label('Website manager'),

                    TextInput::make('microweber.website_manager_url')
                        ->label('Website Manager Url'),

                    Select::make('microweber.get_package_manager_urls_from_website_manager')
                        ->options([
                            'no' => 'No',
                            'yes' => 'Yes',
                        ])
                        ->label('Get package manager urls from website manager'),

                    Select::make('microweber.allow_resellers_to_use_their_own_white_label')
                        ->options([
                            'no' => 'No',
                            'yes' => 'Yes',
                        ])
                        ->label('Allow resellers to use their own White Label'),

                ]),
        ];
    }
}
