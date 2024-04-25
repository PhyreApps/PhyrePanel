<?php

namespace App\Console\Commands;

use App\Models\Backup;
use App\Models\HostingSubscription;
use App\Models\HostingSubscriptionBackup;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RunHostingSubscriptionsBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:run-hosting-subscriptions-backup';

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
        // Delete backups older than 7 days
        $findBackupsForDeleting = HostingSubscriptionBackup::where('created_at', '<', now()->subDays(7))->get();
        foreach ($findBackupsForDeleting as $backup) {
            $backup->delete();
        }

        // Find Hosting Subscriptions
        $findHostingSubscriptions = HostingSubscription::all();
        if ($findHostingSubscriptions->count() > 0) {
            foreach ($findHostingSubscriptions as $hostingSubscription) {

                $findBackup = HostingSubscriptionBackup::where('hosting_subscription_id', $hostingSubscription->id)
                    ->where('backup_type', 'full')
                    ->where('created_at', '>=', Carbon::now()->subHours(24))
                    ->first();
                if (! $findBackup) {
                    $backup = new HostingSubscriptionBackup();
                    $backup->hosting_subscription_id = $hostingSubscription->id;
                    $backup->backup_type = 'full';
                    $backup->save();
                } else {
                    $this->error('Backup already exists for ' . $hostingSubscription->domain);
                    $this->error('Created before: ' . $findBackup->created_at->diffForHumans());
                }
            }
        }


        // Check for pending backups
        $getPendingBackups = HostingSubscriptionBackup::where('status', 'pending')
            ->get();

        if ($getPendingBackups->count() > 0) {
            foreach ($getPendingBackups as $pendingBackup) {
                $pendingBackup->startBackup();
                $this->info('Backup started.. ');
            }
        }

        // Check for processing backups
        $getRunningBackups = HostingSubscriptionBackup::where('status', 'processing')->get();
        if ($getRunningBackups->count() > 0) {
            foreach ($getRunningBackups as $runningBackup) {
                $runningBackup->checkBackup();
                $this->info('Checking backup status...');
            }
        }

    }
}
