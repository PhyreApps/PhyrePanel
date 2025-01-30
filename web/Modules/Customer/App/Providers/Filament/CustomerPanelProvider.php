<?php

namespace Modules\Customer\App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Modules\Customer\App\Http\Middleware\CustomerAuthenticate;

class CustomerPanelProvider extends PanelProvider
{
    private string $module = 'Customer';

    public function panel(Panel $panel): Panel
    {
        $moduleNamespace = $this->getModuleNamespace();

        $defaultColor = Color::Yellow;
        $brandLogo = asset('images/phyre-logo.svg');

        $isAppInstalled = file_exists(storage_path('installed'));
        if ($isAppInstalled && !(php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg')) {
            if (setting('general.brand_logo_url')) {
                $brandLogo = setting('general.brand_logo_url');
            }
            if (setting('general.brand_primary_color')) {
                $defaultColor = Color::hex(setting('general.brand_primary_color'));
            }
        }

        return $panel
            ->id('customer::admin')
            ->path('customer')
            ->login()
            ->font('Albert Sans')
            ->sidebarWidth('15rem')
            //  ->brandLogo(fn () => view('filament.admin.logo'))
            ->brandLogo($brandLogo)
            ->brandLogoHeight('2.2rem')
            ->colors([
                'primary'=>$defaultColor,
            ])
            ->navigationGroups([
                'Hosting',
                'Git'
            ])
            ->discoverResources(in: module_path($this->module, 'App/Filament/Resources'), for: "$moduleNamespace\\App\\Filament\\Resources")
            ->discoverPages(in: module_path($this->module, 'App/Filament/Pages'), for: "$moduleNamespace\\App\Filament\\Pages")
//            ->pages([
//                Pages\Dashboard::class,
//            ])
            ->discoverWidgets(in: module_path($this->module, 'App/Filament/Widgets'), for: "$moduleNamespace\\App\Filament\\Widgets")
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authGuard('web_customer')
            ->authMiddleware([
                CustomerAuthenticate::class,
            ]);
    }

    protected function getModuleNamespace(): string
    {
        return config('modules.namespace').'\\'.$this->module;
    }
}
