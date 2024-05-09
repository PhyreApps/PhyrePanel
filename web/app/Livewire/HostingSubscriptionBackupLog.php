<?php

namespace App\Livewire;

use Livewire\Component;

class HostingSubscriptionBackupLog extends Component
{
    public $hostingSubscriptionBackupId;

    public $backupLog;

    public function pullBackupLog()
    {
        $findHsb = \App\Models\HostingSubscriptionBackup::where('id', $this->hostingSubscriptionBackupId)->first();
        if ($findHsb) {
            $backupLog = $findHsb->path . '/backup.log';
            if (file_exists($backupLog)) {
                $backupLogContent = file_get_contents($backupLog);
                // Get last 1000 lines of the log
                $backupLogContent = substr($backupLogContent, -5000, 5000);
                $backupLogContent = str_replace("\n", "<br>", $backupLogContent);

                $this->backupLog = $backupLogContent;
            }
        }
    }
    public function mount($hostingSubscriptionBackupId)
    {
        $this->hostingSubscriptionBackupId = $hostingSubscriptionBackupId;
    }

    public function view()
    {
        return view('livewire.hosting-subscription-backup-log');
    }
}
