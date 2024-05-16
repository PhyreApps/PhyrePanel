<?php

namespace App\Backup\Abstract;

abstract class BackupConfigBase
{
    public array $excludePaths = [
    ];

    public function getConfig()
    {
        $configValues = [];
        $configValues['excludePaths'] = $this->excludePaths;

        return $configValues;
    }
}
