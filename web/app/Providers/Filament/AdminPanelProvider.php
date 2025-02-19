<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Settings\GeneralSettings;
use App\Filament\Widgets\CustomersCount;
use App\Filament\Widgets\ServerDiskUsageStatistic;
use App\Filament\Widgets\ServerMemoryStatistic;
use App\Filament\Widgets\ServerMemoryStatisticCount;
use App\Filament\Widgets\Websites;
use App\Models\Module;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Outerweb\FilamentSettings\Filament\Plugins\FilamentSettingsPlugin;
use Tapp\FilamentAuthenticationLog\FilamentAuthenticationLogPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
         $panel->default()
            ->darkMode(true)
            ->id('admin')
            ->path('admin')
            ->login()
             ->renderHook(
                 name: PanelsRenderHook::TOPBAR_START,
                 hook: fn (): string => Blade::render('@livewire(\'quick-service-restart-menu\')')
             )
           //  ->topNavigation()
             ->unsavedChangesAlerts()
             ->globalSearch(true)
             ->databaseNotifications()
            ->font('Exo 2')
            ->sidebarWidth('15rem')
          //  ->brandLogo(fn () => view('filament.admin.logo'))
            ->navigationGroups([
                'Hosting Services' => NavigationGroup::make()->label('Hosting Services'),
                // 'Docker' => NavigationGroup::make()->label('Docker'),
                'SSL Manager'=> NavigationGroup::make()->label('SSL Manager')
//                    ->icon('ssl_manager-logo')
                ,
                'Server Management' => NavigationGroup::make()->label('Server Management'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->plugins([
                //  FilamentAuthenticationLogPlugin::make(),
                FilamentApexChartsPlugin::make(),
                FilamentSettingsPlugin::make()->pages([
                    GeneralSettings::class,
                ]),
            ])
         //   ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                ServerDiskUsageStatistic::class,
                ServerMemoryStatistic::class,
                // ServerMemoryStatisticCount::class,
                CustomersCount::class,
                Websites::class,
                // Widgets\AccountWidget::class,
                //                Widgets\FilamentInfoWidget::class,
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
            ->authMiddleware([
                Authenticate::class,
            ]);

        $defaultColor = Color::Yellow;
        $brandLogo = null;
        $brandName = null;
        $isAppInstalled = file_exists(storage_path('installed'));
        if ($isAppInstalled && !(php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg')) {
            if (setting('general.brand_logo_url')) {
                $brandLogo = setting('general.brand_logo_url');
            }
            if (setting('general.brand_name')) {
                $brandName = setting('general.brand_name');
            }
            if (setting('general.brand_primary_color')) {
                $defaultColor = Color::hex(setting('general.brand_primary_color'));
            }
            $findModules = Module::where('installed', 1)->get();

            if ($findModules->count() > 0) {
                foreach ($findModules as $module) {

                    // Search in main path
                    $modulePathClusters = module_path($module->name, 'Filament/Clusters');
                    if (is_dir($modulePathClusters)) {
                        $panel->discoverClusters(in: $modulePathClusters, for: 'Modules\\' . $module->name . '\\Filament\\Clusters');
                    }
                    $modulePathPages = module_path($module->name, 'Filament/Pages');
                    if (is_dir($modulePathPages)) {
                        $panel->discoverPages(in: $modulePathPages, for: 'Modules\\' . $module->name . '\\Filament\\Pages');
                    }
                    $modulePathResources = module_path($module->name, 'Filament/Resources');
                    if (is_dir($modulePathResources)) {
                        $panel->discoverResources(in: $modulePathResources, for: 'Modules\\' . $module->name . '\\Filament\\Resources');
                    }

                    // Search in app path
                    $modulePathClusters = module_path($module->name, 'App/Filament/Clusters');
                    if (is_dir($modulePathClusters)) {
                        $panel->discoverClusters(in: $modulePathClusters, for: 'Modules\\' . $module->name . '\\App\\Filament\\Clusters');
                    }
                    $modulePathPages = module_path($module->name, 'App/Filament/Pages');
                    if (is_dir($modulePathPages)) {
                        $panel->discoverPages(in: $modulePathPages, for: 'Modules\\' . $module->name . '\\App\\Filament\\Pages');
                    }
                    $modulePathResources = module_path($module->name, 'App/Filament/Resources');
                    if (is_dir($modulePathResources)) {
                        $panel->discoverResources(in: $modulePathResources, for: 'Modules\\' . $module->name . '\\App\\Filament\\Resources');
                    }
                }
            }
        }

        if ($brandLogo) {
            $panel->brandLogo($brandLogo);
        } else if ($brandName) {
            $panel->brandName($brandName);
        } else {
            $panel->brandLogo(asset('images/phyre-logo.svg'));
        }

        $panel->brandLogoHeight('2.2rem')
        ->colors([
            'primary'=>$defaultColor,
        ]);

        $panel->renderHook(
            name: PanelsRenderHook::BODY_START,
            hook: fn (): string => Blade::render('@livewire(\'jobs-queue-notifications\')')
        );

        return $panel;
    }
}
