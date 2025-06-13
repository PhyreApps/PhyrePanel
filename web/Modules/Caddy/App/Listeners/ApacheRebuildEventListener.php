<?php

namespace Modules\Caddy\App\Listeners;


use App\Events\DomainIsCreated;
use Modules\Caddy\App\Jobs\CaddyBuild;

class ApacheRebuildEventListener
{
    /**
     * Handle the event.
     */
    public function handle($event): void
    {

        $this->rebuildCaddyIfEnabled();


    }

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
