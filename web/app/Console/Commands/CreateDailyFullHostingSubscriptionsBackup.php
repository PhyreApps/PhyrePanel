<?php

namespace App\Console\Commands;

use App\Jobs\ProcessHostingSubscriptionBackup;
use App\Models\Backup;
use App\Models\HostingSubscription;
use App\Models\HostingSubscriptionBackup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Database\Migrations\Migration;



class CreateDailyFullHostingSubscriptionsBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:create-daily-full-hosting-subscriptions-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find Hosting Subscriptions
        $findHostingSubscriptions = HostingSubscription::all();
        if ($findHostingSubscriptions->count() > 0) {
            foreach ($findHostingSubscriptions as $hostingSubscription) {

                $findBackup = HostingSubscriptionBackup::where('hosting_subscription_id', $hostingSubscription->id)
                    ->where('backup_type', 'full')
                    ->where('created_at', '>=', Carbon::now()->subHours(24))
                    ->first();
                if (! $findBackup) {
                    ProcessHostingSubscriptionBackup::dispatch($hostingSubscription->id);
                } else {
                    $this->error('Backup already exists for ' . $hostingSubscription->domain);
                    $this->error('Created before: ' . $findBackup->created_at->diffForHumans());
                }
            }
        }

    }
}
