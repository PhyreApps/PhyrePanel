<?php

namespace Modules\Microweber\Listeners;

use App\Events\DomainIsChanged;
use App\Models\HostingSubscription;
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
        $findHostingSubscription = HostingSubscription::where('id', $event->domain->id)->first();
        if (!$findHostingSubscription) {
            $this->info('Hosting subscription not found: ' . $event->domain->id);
            return;
        }
        $findInstallation = MicroweberInstallation::where('domain_id', $event->domain->id)
            ->first();
        $chown_user = $findHostingSubscription->system_username;

        $chown_path = $findMicroweberInstallation->installation_path;

        shell_exec('php ' . $findMicroweberInstallation->installation_path . '/artisan cache:clear');

        //fix permissions
        shell_exec('chown -R ' . $chown_user . ':' . $chown_user . ' ' . $chown_path);

        // chmod
        shell_exec('chmod -R 755 ' . $chown_path);




    }
}
