<?php

namespace App\Filament\Pages\Settings;

use App\Helpers;
use App\Jobs\ApacheBuild;
use App\MasterDomain;
use Closure;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Illuminate\Support\Facades\Storage;
use Monarobase\CountryList\CountryList;
use Modules\Caddy\App\Jobs\CaddyBuild;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;

class GeneralSettings extends BaseSettings
{
    protected static bool $shouldRegisterNavigation = false;

    public function save(): void
    {
        $oldMasterDomain = setting('general.master_domain');

        parent::save();

        // Overwrite supervisor config file
        $workersCount = (int)setting('general.supervisor_workers_count');
        $supervisorConf = view('actions.samples.ubuntu.supervisor-conf', [
            'workersCount' => $workersCount
        ])->render();

        // Overwrite supervisor config file
        file_put_contents('/etc/supervisor/conf.d/phyre.conf', $supervisorConf);

        // Restart supervisor
        shell_exec('service supervisor restart');

        file_put_contents('/var/www/html/index.html', setting('general.master_domain_page_html'));

        $rebuildApache = false;
        if ($oldMasterDomain != setting('general.master_domain')) {
            $rebuildApache = true;
        }
        if ($rebuildApache) {
            $apacheBuild = new ApacheBuild(true);
            $apacheBuild->handle();
        }

        // Rebuild Caddy configuration if enabled
        if (setting('caddy.enabled')) {
            $caddyBuild = new CaddyBuild(true);
            $caddyBuild->handle();
        }

    }

    public function rebuildApacheConfig(): void
    {
        ApacheBuild::dispatchSync();

        Notification::make()
            ->title('Apache configuration rebuilt successfully')
            ->success()
            ->send();
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

                            TextInput::make('general.master_domain')->regex('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i'),

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

                    Tabs\Tab::make('Apache ports')
                        ->schema([
                            TextInput::make('general.apache_http_port')
                                ->default('80')
                                ->numeric()
                                ->helperText('Default is 80. When Caddy is enabled, consider using 8080.'),
                            TextInput::make('general.apache_https_port')
                                ->default('443')
                                ->numeric()
                                ->helperText('Default is 443. When Caddy is enabled, SSL is handled by Caddy.'),
                            Checkbox::make('general.apache_ssl_disabled')
                                ->label('Disable SSL')
                                ->helperText('If checked, the Apache server will not listen on port 443 for HTTPS requests. Recommended when using Caddy.'),
                            Actions::make([
                                Actions\Action::make('rebuildApache')
                                    ->label('Rebuild Apache Configuration')
                                    ->button()
                                    ->action(fn() => $this->rebuildApacheConfig())
                            ]),
                        ]),

                    Tabs\Tab::make('Caddy Integration')
                        ->schema([
                            Checkbox::make('caddy.enabled')
                                ->label('Enable Caddy as Reverse Proxy')
                                ->helperText('Enable Caddy to handle SSL termination and proxy requests to Apache')
                                ->live(),
                            
                            TextInput::make('caddy.apache_proxy_port')
                                ->label('Apache Proxy Port')
                                ->default('8080')
                                ->numeric()
                                ->visible(fn (Get $get) => $get('caddy.enabled'))
                                ->helperText('Port where Apache will listen for HTTP requests from Caddy'),
                            
                            TextInput::make('caddy.email')
                                ->label('ACME Email for SSL')
                                ->visible(fn (Get $get) => $get('caddy.enabled'))
                                ->default(fn() => setting('general.master_email'))
                                ->helperText('Email for Let\'s Encrypt SSL certificate registration'),
                            
                            Checkbox::make('caddy.auto_configure_apache')
                                ->label('Auto-configure Apache ports')
                                ->visible(fn (Get $get) => $get('caddy.enabled'))
                                ->default(true)
                                ->helperText('Automatically set Apache to use non-standard ports and disable SSL when Caddy is enabled'),
                            
                            Actions::make([
                                Actions\Action::make('setupCaddyIntegration')
                                    ->label('Setup Caddy Integration')
                                    ->button()
                                    ->visible(fn (Get $get) => $get('caddy.enabled'))
                                    ->action(function () {
                                        // Auto-configure Apache for Caddy integration
                                        if (setting('caddy.auto_configure_apache')) {
                                            setting(['general.apache_http_port' => setting('caddy.apache_proxy_port', '8080')]);
                                            setting(['general.apache_ssl_disabled' => true]);
                                            
                                            // Rebuild both Apache and Caddy
                                            $apacheBuild = new ApacheBuild(true);
                                            $apacheBuild->handle();
                                        }
                                        
                                        $caddyBuild = new CaddyBuild(true);
                                        $caddyBuild->handle();
                                        
                                        Notification::make()
                                            ->title('Caddy integration configured successfully')
                                            ->success()
                                            ->send();
                                    })
                            ]),
                        ]),

                    Tabs\Tab::make('Backups')
                        ->schema([
                            TextInput::make('general.backup_path')
                                ->default(Storage::path('backups'))
                        ]),

                    Tabs\Tab::make('Supervisor')
                        ->schema([
                            TextInput::make('general.supervisor_workers_count')
                                ->numeric()
                                ->helperText('Number of workers to run supervisor processes. Default is 4.')
                                ->default(4)
                        ]),
                ]),
        ];
    }
}
