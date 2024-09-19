<?php

namespace Modules\Email;

use App\ModulePostInstall;

class PostInstall  extends ModulePostInstall
{
    public $supportLog = true;
    public function run()
    {
        $installShellFile = base_path('Modules/Email/shell/install.sh');

        shell_exec("chmod +x $installShellFile");
        shell_exec("bash $installShellFile >> $this->logFile &");

    }
}
