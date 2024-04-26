<?php

namespace App;

abstract class ModulePostInstall
{
    public $supportLog = false;

    public function isSupportLog()
    {
        return $this->supportLog;
    }
    public function setLogFile($logFile)
    {
        $this->logFile = $logFile;
    }
}
