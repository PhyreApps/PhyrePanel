<?php

namespace Modules\Caddy\App\Listeners;

use App\Events\DomainIsCreated;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Domain;
use App\Models\HostingPlan;
use App\Models\HostingSubscription;
use App\Services\HostingSubscriptionService;
use App\SupportedApplicationTypes;
use Illuminate\Support\Str;
use MicroweberPackages\SharedServerScripts\MicroweberWhitelabelSettingsUpdater;
use MicroweberPackages\SharedServerScripts\MicroweberWhitelabelWebsiteApply;
use Modules\Microweber\App\Models\MicroweberInstallation;

class DomainIsCreatedListener extends DomainEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DomainIsCreated $event): void
    {
    /*    if (setting('caddy.enabled')) {
            $domain = $event->model;
            $findHostingSubscription = HostingSubscription::where('id', $domain->hosting_subscription_id)->first();
            if ($findHostingSubscription) {
                $sytemUsername = $findHostingSubscription->system_username;
                //add caddy to the user group

                shell_exec('usermod -aG caddy ' . $sytemUsername);
                shell_exec('usermod -aG ' . $sytemUsername . ' caddy');
            }
        }*/

        $this->rebuildCaddyIfEnabled();


    }
}
