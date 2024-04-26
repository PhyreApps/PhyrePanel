<?php

namespace Modules\Docker;

use App\ModulePostInstall;

class PostInstall  extends ModulePostInstall
{
    public $supportLog = true;
    public function run()
    {
        $installDockerShellFile = base_path('Modules/Docker/shell-scripts/install-docker.sh');

        shell_exec("chmod +x $installDockerShellFile");
        shell_exec("bash $installDockerShellFile >> $this->logFile &");

    }
}
