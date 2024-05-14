<?php

namespace App\Console\Commands;

use App\Models\Backup;
use App\Models\HostingSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RunBackupChecks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:run-backup-checks';

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
        $findBackupsForDeleting = Backup::where('created_at', '<', now()->subDays(7))->get();
        foreach ($findBackupsForDeleting as $backup) {
            $backup->delete();
        }

        // Check for pending backups
        $getPendingBackups = Backup::where('status', 'pending')
            ->get();

        if ($getPendingBackups->count() > 0) {
            if ($getPendingBackups->count() > 1) {
                $this->info('Multiple backups are pending...');
            } else {
                foreach ($getPendingBackups as $pendingBackup) {
                    $pendingBackup->startBackup();
                    $this->info('Backup started.. ');
                }
            }
        }

        // Check for processing backups
        $getRunningBackups = Backup::where('status', 'processing')->get();
        if ($getRunningBackups->count() > 0) {
            foreach ($getRunningBackups as $runningBackup) {
                $runningBackup->checkBackup();
                $this->info('Checking backup status...');
            }
        }

    }
}
