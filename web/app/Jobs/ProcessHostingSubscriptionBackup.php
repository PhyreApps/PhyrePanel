<?php

namespace App\Jobs;

use App\Models\HostingSubscriptionBackup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessHostingSubscriptionBackup implements ShouldQueue
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
        echo "Backing up hosting subscription with ID: {$this->hostingSubscriptionId}\n";

        sleep(3);
//
//        $backup = new HostingSubscriptionBackup();
//        $backup->hosting_subscription_id = $this->hostingSubscriptionId;
//        $backup->backup_type = 'full';
//        $backup->save();
    }
}
