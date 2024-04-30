<?php

namespace App\Providers;

use App\Events\ModelDomainCreated;
use App\Events\ModelDomainDeleting;
use App\Events\ModelHostingSubscriptionCreated;
use App\Events\ModelHostingSubscriptionDeleting;
use App\Listeners\ModelDomainCreatedListener;
use App\Listeners\ModelDomainDeletingListener;
use App\Listeners\ModelHostingSubscriptionCreatingListener;
use App\Listeners\ModelHostingSubscriptionDeletingListener;
use App\Livewire\Components\QuickServiceRestartMenu;
use App\Models\Domain;
use App\Models\HostingSubscription;
use App\Policies\CustomerPolicy;
use BladeUI\Icons\Factory;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // This allows us to generate a temporary url for backups downloading
        Storage::disk('backups')->buildTemporaryUrlsUsing(function ($path, $expiration, $options) {
            return URL::temporarySignedRoute(
                'backup.download',
                $expiration,
                array_merge($options, ['path' => $path])
            );
        });

        // Register Phyre Icons set
        $this->callAfterResolving(Factory::class, function (Factory $factory) {
            $factory->add('phyre', [
                'path' => __DIR__ . '/../../resources/phyre-svg',
                'prefix' => 'phyre',
            ]);
        });

        App::singleton('file_manager_api', function () {
            return new \App\FileManagerApi();
        });

        App::singleton('virtualHostManager', function () {
            return new \App\VirtualHosts\ApacheVirtualHostManager();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Filament::serving(function () {
            // Using Vite
            Filament::registerViteTheme('resources/css/app.css');
        });

        Livewire::component('quick-service-restart-menu', QuickServiceRestartMenu::class);

        Gate::define('delete-customer', [CustomerPolicy::class, 'delete']);

        if (is_file(storage_path('installed'))) {
            $getDomains = Domain::all();
            if ($getDomains->count() > 0) {
                foreach ($getDomains as $domain) {
                    $this->app['config']["filesystems.disks.backups_" . Str::slug($domain->domain)] = [
                        'driver' => 'local',
                        'root' => $domain->domain_root . '/backups',
                    ];
                }
            }
        }
    }
}
