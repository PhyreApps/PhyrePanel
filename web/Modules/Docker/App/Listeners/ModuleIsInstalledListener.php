<?php

namespace Modules\Docker\App\Listeners;


use App\Events\ModuleIsInstalled;
use Illuminate\Support\Facades\Artisan;

class ModuleIsInstalledListener
{
    public function handle(ModuleIsInstalled $event): void
    {
        if ($event->module->name === 'Docker') {
            Artisan::call('docker:search-images redis');
        }
    }
}
