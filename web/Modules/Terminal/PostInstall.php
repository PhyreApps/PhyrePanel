<?php

namespace Modules\Terminal;

use App\ModulePostInstall;

class PostInstall  extends ModulePostInstall
{
    public $supportLog = true;
    public function run()
    {
        $installDockerShellFile = base_path('Modules/Terminal/shell-scripts/install-nodejs.sh');

        shell_exec("chmod +x $installDockerShellFile");
        shell_exec("bash $installDockerShellFile >> $this->logFile &");

    }
}
