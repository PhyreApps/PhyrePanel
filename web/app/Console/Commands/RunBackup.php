<?php

namespace App\Console\Commands;

use App\Models\Backup;
use App\Models\HostingSubscription;
use Illuminate\Console\Command;

class RunBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:run-backup';

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

        $getBackups = Backup::where('backup_type', 'hosting_subscription')->get();
        if ($getBackups->count() > 0) {
            foreach ($getBackups as $backup) {
                $status = $backup->startBackup();
            }
        }

    }
}
