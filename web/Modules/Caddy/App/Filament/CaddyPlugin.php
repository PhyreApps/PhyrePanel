<?php

namespace Modules\Caddy\App\Filament;

use Coolsam\Modules\Concerns\ModuleFilamentPlugin;
use Filament\Contracts\Plugin;
use Filament\Panel;

class CaddyPlugin implements Plugin
{
    use ModuleFilamentPlugin;

    public function getModuleName(): string
    {
        return 'Caddy';
    }

    public function getId(): string
    {
        return 'caddy';
    }

    public function boot(Panel $panel): void
    {
        // TODO: Implement boot() method.
    }
}
