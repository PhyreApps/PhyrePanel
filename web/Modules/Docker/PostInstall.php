<?php

namespace Modules\Docker;

class PostInstall
{
    public function run()
    {
        $installDockerShellFile = base_path('Modules/Docker/shell-scripts/install-docker.sh');

        shell_exec("chmod +x $installDockerShellFile");
        shell_exec("bash $installDockerShellFile");

    }
}
