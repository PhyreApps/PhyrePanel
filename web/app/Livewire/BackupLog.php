<?php

namespace App\Livewire;

use Livewire\Component;

class BackupLog extends Component
{
    public $backupId;

    public $backupLog;

    public function pullBackupLog()
    {

        $findBackup = \App\Models\Backup::where('id', $this->backupId)->first();
        if ($findBackup) {
            $backupLog = $findBackup->path . '/backup.log';
            if (file_exists($backupLog)) {
                $backupLogContent = file_get_contents($backupLog);
                // Get last 1000 lines of the log
                $backupLogContent = substr($backupLogContent, -5000, 5000);
                $backupLogContent = str_replace("\n", "<br>", $backupLogContent);

                $this->backupLog = $backupLogContent;
            }
        }
    }
    public function mount($backupId)
    {
        $this->backupId = $backupId;
    }

    public function render()
    {
        return view('livewire.hosting-subscription-backup-log');
    }
}
