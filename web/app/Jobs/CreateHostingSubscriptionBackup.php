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

class CreateHostingSubscriptionBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $hostingSubscriptionId;

    /**
     * Create a new job instance.
     */
    public function __construct($hostingSubscriptionId)
    {
        $this->hostingSubscriptionId = $hostingSubscriptionId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        echo "Backup hosting subscription with ID: {$this->hostingSubscriptionId}\n";

        $backup = new HostingSubscriptionBackup();
        $backup->hosting_subscription_id = $this->hostingSubscriptionId;
        $backup->backup_type = 'full';
        $backup->save();

        $backupDone = false;
        $findHostingSubscriptionBackup = HostingSubscriptionBackup::where('id', $backup->id)->first();
        if ($findHostingSubscriptionBackup) {

            for ($i = 0; $i < 200; $i++) {
                echo "Check: ".$i." | Checking backup status...\n";
                $findHostingSubscriptionBackup->checkBackup();
                if ($findHostingSubscriptionBackup->status == BackupStatus::Completed) {
                    echo "Backup completed\n";
                    $backupDone = true;
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
