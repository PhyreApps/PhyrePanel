<?php

namespace Modules\Caddy\App\Listeners;


use App\Events\DomainIsCreated;
use Modules\Caddy\App\Jobs\CaddyBuild;

class DomainEventListener
{
    /**
     * Handle the event.
     */


    /**
     * Rebuild Caddy configuration if Caddy is enabled and auto-rebuild is on.
     */
    public function rebuildCaddyIfEnabled(): void
    {
        if (setting('caddy.enabled')) {
            CaddyBuild::dispatchSync();
        }
    }
}
