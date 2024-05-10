<?php

namespace App\Livewire;

use Livewire\Component;

class HostingSubscriptionBackupLog extends Component
{
    public $hostingSubscriptionBackupId;

    public $backupLog;

    public $backupLogFile = '';

    public function pullBackupLog()
    {
        if ($this->backupLogFile) {
            if (file_exists($this->backupLogFile)) {

                $backupLogContent = shell_exec('tail -n 1000 ' . $this->backupLogFile);
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

        $findHsb = \App\Models\HostingSubscriptionBackup::where('id', $this->hostingSubscriptionBackupId)->first();
        if ($findHsb) {
            $this->backupLogFile = $findHsb->path . '/backup.log';
            $this->pullBackupLog();
        }
    }

    public function render()
    {
        return view('livewire.hosting-subscription-backup-log');
    }
}
