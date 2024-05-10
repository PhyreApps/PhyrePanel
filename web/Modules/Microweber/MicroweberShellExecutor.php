<?php

namespace Modules\Microweber;

use MicroweberPackages\SharedServerScripts\Shell\Adapters\NativeShellExecutor;
use Symfony\Component\Process\Process;

class MicroweberShellExecutor extends NativeShellExecutor
{
    public function executeCommand(array $args, $cwd = null, $env = null)
    {
        // Escape shell arguments
        $args = array_map('escapeshellarg', $args);
        $command = implode(' ', $args);

        if (!empty($cwd)) {
            $command = 'cd ' . escapeshellarg($cwd) . ' && ' . $command;
        }

        return shell_exec($command);
    }

}
