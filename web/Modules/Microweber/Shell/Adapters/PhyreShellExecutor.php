<?php

namespace Modules\Microweber\Shell\Adapters;

use App\ShellApi;
use MicroweberPackages\SharedServerScripts\Shell\Adapters\IShellExecutor;

class PhyreShellExecutor implements IShellExecutor
{
    /**
     * @return mixed
     */
    public function executeFile(string $file, array $args)
    {
        $output = ShellApi::exec($file, $args);

        return $output;
    }

    public function executeCommand(array $command, $path, $args)
    {
        $commandAsLine = implode(' ', $command);

        //        dd([
        //            'command' => $command,
        //            'path' => $path,
        //            'args' => $args,
        //            'commandAsLine' => $commandAsLine
        //        ]);
        return ShellApi::exec($commandAsLine);
    }
}
