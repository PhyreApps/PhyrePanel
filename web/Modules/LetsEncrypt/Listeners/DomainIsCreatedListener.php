<?php

namespace Modules\LetsEncrypt\Listeners;

use App\ApiClient;
use App\Events\DomainIsCreated;
use App\Models\DomainSslCertificate;
use App\Models\HostingPlan;
use App\Models\HostingSubscription;
use App\Settings;
use App\ShellApi;

class DomainIsCreatedListener
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
        $findDomain = \App\Models\Domain::where('id', $event->model->id)->first();
        if (! $findDomain) {
            return;
        }

        $findHostingSubscription = HostingSubscription::where('id', $findDomain->hosting_subscription_id)->first();
        if (! $findHostingSubscription) {
            return;
        }
        $findHostingPlan = HostingPlan::where('id', $findHostingSubscription->hosting_plan_id)->first();
        if (! $findHostingPlan) {
            return;
        }

        if (! in_array('letsencrypt', $findHostingPlan->additional_services)) {
            return;
        }

        try {
            $secureDomain = new \Modules\LetsEncrypt\Jobs\LetsEncryptSecureDomain($findDomain->id);
            $secureDomain->handle();
        } catch (\Exception $e) {
            return;
        }

    }
}
