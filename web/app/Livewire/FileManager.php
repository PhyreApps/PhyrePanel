<?php

namespace App\Livewire;

use App\Models\Domain;
use Livewire\Component;

class FileManager extends Component
{
    public $hostingSubscriptionId;

    public $currentRealPath;

    public $currentPath;

    public function mount($hostingSubscriptionId)
    {
        $this->hostingSubscriptionId = $hostingSubscriptionId;
    }

    public function render()
    {
        $findDomain = Domain::where('hosting_subscription_id', $this->hostingSubscriptionId)
            ->where('is_main', 1)
            ->first();

        if ($findDomain) {
            if (!$this->currentRealPath) {
                $this->currentRealPath = $findDomain->home_root;
            }
        }

        $all = [];
        $files = [];
        $folders = [];
        if ($this->currentRealPath) {
            $scanFiles = scandir($this->currentRealPath);
            foreach ($scanFiles as $scanFile) {
                if ($scanFile == '.' || $scanFile == '..') {
                    continue;
                }
                $append = [
                    'name' => $scanFile,
                    'path' => $this->currentRealPath . '/' . $scanFile,
                    'is_dir' => is_dir($this->currentRealPath . '/' . $scanFile)
                ];
                if ($append['is_dir']) {
                    $folders[] = $append;
                } else {
                    $files[] = $append;
                }
            }
        }
        $all = array_merge($folders, $files);

        return view('livewire.file-manager.index', [
            'files'=>$all
        ]);
    }
}
