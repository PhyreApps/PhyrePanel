<?php

namespace App;

abstract class ModulePostInstall
{
    public function setLogFile($logFile)
    {
        $this->logFile = $logFile;
    }
}
