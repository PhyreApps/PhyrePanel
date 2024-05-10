<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class RestoreBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $backupFile = null;

    public static $displayName = 'Restore Backup';
    public static $displayDescription = 'Restore backup file...';

    /**
     * Create a new job instance.
     */
    public function __construct($backupFile)
    {
        $this->backupFile = $backupFile;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        echo "Restoring backup file: " . $this->backupFile . "\n";

        $file = Storage::disk('local')->get($this->backupFile);
        if (!$file) {
            echo "Backup file not found\n";
            return;
        }

        sleep(14);


    }
}
