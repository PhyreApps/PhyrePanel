<?php

namespace App\Providers;

use App\BackupStorage;
use App\Events\ModelDomainCreated;
use App\Events\ModelDomainDeleting;
use App\Events\ModelHostingSubscriptionCreated;
use App\Events\ModelHostingSubscriptionDeleting;
use App\Listeners\ModelDomainCreatedListener;
use App\Listeners\ModelDomainDeletingListener;
use App\Listeners\ModelHostingSubscriptionCreatingListener;
use App\Listeners\ModelHostingSubscriptionDeletingListener;
use App\Livewire\Components\QuickServiceRestartMenu;
use App\Livewire\HostingSubscriptionBackupLog;
use App\Models\Domain;
use App\Models\HostingSubscription;
use App\Policies\CustomerPolicy;
use BladeUI\Icons\Factory;
use Filament\Facades\Filament;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
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
        Livewire::component('hosting-subscription-backup-log', HostingSubscriptionBackupLog::class);

        Gate::define('delete-customer', [CustomerPolicy::class, 'delete']);

    }
}
