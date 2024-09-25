<?php

namespace Modules\LetsEncrypt;

use App\ModulePostInstall;

class PostInstall  extends ModulePostInstall
{
    public $supportLog = true;
    public function run()
    {
        $installDockerShellFile = base_path('Modules/LetsEncrypt/shell/install-letsencrypt.sh');

        shell_exec("chmod +x $installDockerShellFile");
        shell_exec("bash $installDockerShellFile >> $this->logFile &");

        return true;
    }
}
