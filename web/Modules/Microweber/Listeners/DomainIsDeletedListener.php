<?php

namespace Modules\Microweber\Listeners;

use App\Events\DomainIsCreated;
use App\Events\DomainIsDeleted;
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
use Modules\Microweber\App\Actions\MicroweberScanner;
use Modules\Microweber\App\Models\MicroweberInstallation;

class DomainIsDeletedListener
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
    public function handle(DomainIsDeleted $event): void
    {

        try {
            $newMwScan = new MicroweberScanner();
            $newMwScan->handle();
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

    }
}
