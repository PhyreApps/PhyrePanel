<?php

namespace App\Console\Commands;

use App\Models\Backup;
use App\Models\HostingSubscriptionBackup;
use App\Models\RemoteBackupServer;
use Illuminate\Console\Command;

class RunUploadBackupsToRemoteServers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:run-upload-backups-to-remote-servers';

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
        $getRemoteBackupServers = RemoteBackupServer::all();
        if ($getRemoteBackupServers->count() > 0) {
            foreach ($getRemoteBackupServers as $remoteBackupServer) {
                $remoteBackupServer->healthCheck();
                if ($remoteBackupServer->status == 'offline') {
                    $this->info('Skipping ' . $remoteBackupServer->name . ' because it is offline.');
                    continue;
                }

                $this->info('Uploading backups to ' . $remoteBackupServer->name . '...');

                $findBackups = Backup::where('status', 'completed')->get();
                if ($findBackups->count() > 0) {
                    foreach ($findBackups as $backup) {
                        $uploadStatus = $remoteBackupServer->uploadFile($backup->filepath, 'phyre-system-backups');
                    }
                } else {
                    $this->info('No backups found to upload.');
                }

                $findHostingSubscriptionBackups = HostingSubscriptionBackup::where('status', 'completed')->get();
                if ($findHostingSubscriptionBackups->count() > 0) {
                    foreach ($findHostingSubscriptionBackups as $hostingSubscriptionBackup) {
                        $uploadStatus = $remoteBackupServer->uploadFile($hostingSubscriptionBackup->filepath, 'phyre-hosting-subscription-backups');
                    }
                } else {
                    $this->info('No hosting subscription backups found to upload.');
                }

            }
        } else {
            $this->info('No remote backup servers found.');
        }
    }
}
