<?php

namespace Modules\Caddy\App\Filament\Pages;

use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Modules\Caddy\App\Jobs\CaddyBuild;
use Modules\Caddy\App\Filament\Clusters\Caddy;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;

class CaddySettings extends BaseSettings
{
    protected static ?string $navigationGroup = 'Caddy';

    protected static ?string $cluster = Caddy::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?int $navigationSort = 1;

    public function save(): void
    {
        parent::save();

        // Rebuild Caddy configuration after saving settings
        if (setting('caddy.enabled')) {
            $caddyBuild = new CaddyBuild(true);
            $caddyBuild->handle();
        }
    }

    public function rebuildCaddyConfig(): void
    {
        CaddyBuild::dispatchSync();

        Notification::make()
            ->title('Caddy configuration rebuilt successfully')
            ->success()
            ->send();
    }

    public function schema(): array
    {
        return [
            Tabs::make('Settings')
                ->schema([
                    Tabs\Tab::make('General')
                        ->schema([
                            Checkbox::make('caddy.enabled')
                                ->label('Enable Caddy')
                                ->helperText('Enable Caddy as reverse proxy for SSL termination. When enabled, Caddy will handle HTTPS and proxy to Apache on HTTP port.'),

                            Checkbox::make('caddy.enable_static_files')
                                ->label('Enable Static File Serving')
                                ->helperText('When enabled, Caddy will directly serve static files from each domain\'s document root for better performance.'),

                            TextInput::make('caddy.email')
                                ->label('ACME Email')
                                ->helperText('Email address for SSL certificate registration with Let\'s Encrypt')
                                ->default(setting('general.master_email')),

                            TextInput::make('caddy.http_port')
                                ->label('Caddy HTTP Port')
                                ->default('80')
                                ->numeric()
                                ->helperText('Port for Caddy to listen on for HTTP requests'),

                            TextInput::make('caddy.https_port')
                                ->label('Caddy HTTPS Port')
                                ->default('443')
                                ->numeric()
                                ->helperText('Port for Caddy to listen on for HTTPS requests'),
                            TextInput::make('caddy.zerossl_api_token')
                                ->label('ZeroSSL API Token')
                                ->password()
                                ->helperText('API token for ZeroSSL. Required for obtaining SSL certificates. Get one from https://app.zerossl.com/developer'),

                            TextInput::make('caddy.cloudflare_api_token')
                                ->label('Cloudflare API Token')
                                ->password()
                                ->helperText('API token for Cloudflare DNS challenge. Required for wildcard SSL certificates. Get one from https://dash.cloudflare.com/profile/api-tokens/'),

                            Checkbox::make('caddy.enable_wildcard_ssl')
                                ->label('Enable Wildcard SSL (Cloudflare)')
                                ->helperText('Prefer wildcard SSL certificates for all domains if possible using Cloudflare DNS plugin.'),

                            TextInput::make('caddy.wildcard_domain')
                                ->label('Wildcard Base Domain')
                                ->placeholder('example.com')
                                ->helperText('Wildcard SSL will only be used for this domain and its subdomains.'),
                        ]),

                    Tabs\Tab::make('Apache Integration')
                        ->schema([
                            Section::make('Apache Configuration')
                                ->description('Configure how Caddy integrates with Apache')
                                ->schema([
                                    TextInput::make('caddy.apache_proxy_port')
                                        ->label('Apache Proxy Port')
                                        ->default('8080')
                                        ->numeric()
                                        ->helperText('Port where Apache listens for HTTP requests (Caddy will proxy to this port)'),

                                    Checkbox::make('caddy.disable_apache_ssl')
                                        ->label('Disable Apache SSL')
                                        ->helperText('Disable SSL on Apache when Caddy is enabled (recommended for proper SSL termination)'),

                                ]),
                        ]),

                    Tabs\Tab::make('Security')
                        ->schema([
                            Section::make('Security Headers')
                                ->description('Configure security headers added by Caddy')
                                ->schema([
                                    Checkbox::make('caddy.enable_hsts')
                                        ->label('Enable HSTS')
                                        ->default(true)
                                        ->helperText('Enable HTTP Strict Transport Security header'),

                                    Checkbox::make('caddy.enable_security_headers')
                                        ->label('Enable Security Headers')
                                        ->default(true)
                                        ->helperText('Enable additional security headers (X-Content-Type-Options, X-Frame-Options, etc.)'),

                                    Checkbox::make('caddy.enable_gzip')
                                        ->label('Enable Gzip Compression')
                                        ->default(true)
                                        ->helperText('Enable gzip compression for responses'),
                                ]),
                        ]),

                    Tabs\Tab::make('Static Files')
                        ->schema([
                            Section::make('Static File Serving')
                                ->description('Configure paths for serving static files directly through Caddy')
                                ->schema([
                                    Checkbox::make('caddy.enable_static_files')
                                        ->label('Enable Static File Serving')
                                        ->helperText('When enabled, Caddy will directly serve static files from each domain\'s document root for better performance.'),

                                    Textarea::make('caddy.static_paths')
                                        ->label('Static File Paths')
                                        ->placeholder('/public/*
/storage/*
/vendor/*
/modules/*
/templates/*
/js/*
/css/*')
                                        ->helperText('Enter paths one per line. These paths will be served directly by Caddy\'s file server from each domain\'s document root.')
                                        ->rows(7)
                                        ->default("/public/*\n/storage/*\n/vendor/*\n/modules/*\n/templates/*\n/js/*\n/css/*"),
                                ]),
                        ]),

                    Tabs\Tab::make('Management')
                        ->schema([
                            Actions::make([
                                Actions\Action::make('rebuildCaddy')
                                    ->label('Rebuild Caddy Configuration')
                                    ->button()
                                    ->action(fn() => $this->rebuildCaddyConfig()),

                                Actions\Action::make('restartCaddy')
                                    ->label('Restart Caddy Service')
                                    ->button()
                                    ->color('warning')
                                    ->requiresConfirmation()
                                    ->action(function () {
                                        shell_exec('systemctl restart caddy');
                                        Notification::make()
                                            ->title('Caddy service restarted successfully')
                                            ->success()
                                            ->send();
                                    }),

                                Actions\Action::make('checkCaddyStatus')
                                    ->label('Check Caddy Status')
                                    ->button()
                                    ->color('gray')
                                    ->action(function () {
                                        $status = shell_exec('systemctl is-active caddy');
                                        $isActive = trim($status) === 'active';

                                        Notification::make()
                                            ->title('Caddy Status: ' . ($isActive ? 'Running' : 'Not Running'))
                                            ->color($isActive ? 'success' : 'danger')
                                            ->send();
                                    }),
                            ]),
                        ]),
                ]),
        ];
    }
}
