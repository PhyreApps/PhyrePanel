<?php

namespace Modules\Caddy\App\Providers;

use App\Events\ApacheRebuildCompleted;
use App\Events\DomainIsChanged;
use App\Events\DomainIsCreated;
use App\Events\DomainIsDeleted;
use BladeUI\Icons\Factory;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Modules\Caddy\App\Listeners\ApacheRebuildEventListener;
use Modules\Caddy\App\Listeners\DomainIsChangedListener;
use Modules\Caddy\App\Listeners\DomainIsCreatedListener;
use Modules\Caddy\App\Listeners\DomainIsDeletedListener;
use Modules\Caddy\App\Providers\RouteServiceProvider;
use Modules\Caddy\App\Listeners\DomainEventListener;

class CaddyServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Caddy';

    protected string $moduleNameLower = 'caddy';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/migrations'));
        $this->registerEventListeners();


        Event::listen(ApacheRebuildCompleted::class,ApacheRebuildEventListener::class);
       Event::listen(DomainIsCreated::class,DomainIsCreatedListener::class);
//        Event::listen(DomainIsChanged::class,DomainIsChangedListener::class);
//        Event::listen(DomainIsDeleted::class,DomainIsDeletedListener::class);

    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        // Register CaddyService
        $this->app->singleton('App\Services\CaddyService', function ($app) {
            return new \App\Services\CaddyService();
        });
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            \Modules\Caddy\App\Console\CaddyRebuild::class,
            \Modules\Caddy\App\Console\CaddyStatus::class,
            \Modules\Caddy\App\Console\CaddyFormat::class,
        ]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([module_path($this->moduleName, 'config/config.php') => config_path($this->moduleNameLower.'.php')], 'config');
        $this->mergeConfigFrom(module_path($this->moduleName, 'config/config.php'), $this->moduleNameLower);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {

        $viewPath = resource_path('views/modules/'.$this->moduleNameLower);
        $sourcePath = module_path($this->moduleName, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->moduleNameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
        $this->loadViewsFrom( $sourcePath, $this->moduleNameLower);

        $componentNamespace = str_replace('/', '\\', config('modules.namespace').'\\'.$this->moduleName.'\\'.config('modules.paths.generator.component-class.path'));
        Blade::componentNamespace($componentNamespace, $this->moduleNameLower);



        // Register Phyre Icons set
        $this->callAfterResolving(Factory::class, function (Factory $factory) {
            $factory->add('caddy', [
                'path' => __DIR__ . '/../../resources/assets/caddy-svg',
                'prefix' => 'caddy',
            ]);
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Register event listeners.
     */
    protected function registerEventListeners(): void
    {
        Event::listen('App\Events\DomainCreated', [DomainEventListener::class, 'handleDomainCreated']);
        Event::listen('App\Events\DomainUpdated', [DomainEventListener::class, 'handleDomainUpdated']);
        Event::listen('App\Events\DomainDeleted', [DomainEventListener::class, 'handleDomainDeleted']);
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->moduleNameLower)) {
                $paths[] = $path.'/modules/'.$this->moduleNameLower;
            }
        }

        return $paths;
    }
}
