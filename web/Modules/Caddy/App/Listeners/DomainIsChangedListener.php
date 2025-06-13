<?php

namespace Modules\Caddy\App\Listeners;

use App\Events\DomainIsChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Microweber\App\Models\MicroweberInstallation;

class DomainIsChangedListener extends DomainEventListener
{

    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     */
    public function handle(DomainIsChanged $event): void
    {
        if (!isset($event->domain->id)) {
            return;
        }

        $this->rebuildCaddyIfEnabled();


    }
}
