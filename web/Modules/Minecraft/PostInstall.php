<?php

namespace Modules\Minecraft;

use App\ModulePostInstall;

class PostInstall  extends ModulePostInstall
{
    public $supportLog = true;
    public function run()
    {
        $installDockerShellFile = base_path('Modules/Minecraft/shell-scripts/install-docker.sh');

        shell_exec("chmod +x $installDockerShellFile");
        shell_exec("bash $installDockerShellFile >> $this->logFile &");

    }
}
