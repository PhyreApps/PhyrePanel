<?php

namespace Modules\Caddy;

use App\ModulePostInstall;

class PostInstall extends ModulePostInstall
{
    public $supportLog = true;

    public function run()
    {
        $installCaddyShellFile = base_path('Modules/Caddy/shell-scripts/install-caddy.sh');

        shell_exec("chmod +x $installCaddyShellFile");
        shell_exec("bash $installCaddyShellFile >> $this->logFile &");

    }
}
