<?php

namespace App\Jobs;

use App\Filament\Enums\BackupStatus;
use App\Models\HostingSubscription;
use App\Models\HostingSubscriptionBackup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessHostingSubscriptionBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $id;

    public static $displayName = 'Hosting Subscription Backup';
    public static $displayDescription = 'Backup hosting subscription files and database...';

    /**
     * Create a new job instance.
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        echo "Backup hosting subscription backup with ID: {$this->id}\n";

        $backupDone = false;
        $findHostingSubscriptionBackup = HostingSubscriptionBackup::where('id', $this->id)->first();
        if ($findHostingSubscriptionBackup) {

            if ($findHostingSubscriptionBackup->status == BackupStatus::Pending) {
                $findHostingSubscriptionBackup->startBackup();
            }

            for ($i = 0; $i < 200; $i++) {
                echo "Check: ".$i." | Checking backup status...\n";
                $findHostingSubscriptionBackup->checkBackup();
                if ($findHostingSubscriptionBackup->status == BackupStatus::Completed) {
                    echo "Backup completed\n";
                    $backupDone = true;
                    break;
                }
                if ($findHostingSubscriptionBackup->status == BackupStatus::Failed) {
                    echo "Backup failed\n";
                    break;
                }

                sleep(4);
            }

            if (! $backupDone) {
                echo "Backup failed\n";
                $findHostingSubscriptionBackup->status = BackupStatus::Failed;
                $findHostingSubscriptionBackup->save();
            }
        }


    }


}
