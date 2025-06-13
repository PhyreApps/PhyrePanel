<?php

namespace Modules\Caddy\App\Listeners;

use App\Events\DomainCreated;
use App\Events\DomainUpdated;
use App\Events\DomainDeleted;
use Modules\Caddy\App\Jobs\CaddyBuild;

class DomainEventListener
{
    /**
     * Handle domain created event.
     */
    public function handleDomainCreated(DomainCreated $event): void
    {
        $this->rebuildCaddyIfEnabled();
    }

    /**
     * Handle domain updated event.
     */
    public function handleDomainUpdated(DomainUpdated $event): void
    {
        $this->rebuildCaddyIfEnabled();
    }

    /**
     * Handle domain deleted event.
     */
    public function handleDomainDeleted(DomainDeleted $event): void
    {
        $this->rebuildCaddyIfEnabled();
    }

    /**
     * Rebuild Caddy configuration if Caddy is enabled and auto-rebuild is on.
     */
    private function rebuildCaddyIfEnabled(): void
    {
        if (setting('caddy.enabled') && setting('caddy.auto_rebuild_on_domain_changes', true)) {
            CaddyBuild::dispatch();
        }
    }
}
