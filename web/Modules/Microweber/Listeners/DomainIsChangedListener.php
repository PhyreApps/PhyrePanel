<?php

namespace Modules\Microweber\Listeners;

use App\Events\DomainIsChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Modules\Microweber\App\Models\MicroweberInstallation;

class DomainIsChangedListener
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

        $findMicroweberInstallation = MicroweberInstallation::where('domain_id', $event->domain->id)->first();
        if (!$findMicroweberInstallation) {
            return;
        }

        shell_exec('php '. $findMicroweberInstallation->installation_path . '/artisan cache:clear');

    }
}
