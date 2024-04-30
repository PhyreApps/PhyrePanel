<?php

namespace App;

use Illuminate\Support\Str;

class ShellApi
{
    public static function safeDelete($pathOrFile, $whiteListedPaths = [])
    {
        if (empty($whiteListedPaths)) {
            throw new \Exception('Whitelist paths cannot be empty');
        }

        $canIDeleteFile = false;
        foreach ($whiteListedPaths as $whiteListedPath) {
            if (Str::of($pathOrFile)->startsWith($whiteListedPath)) {
                $canIDeleteFile = true;
                break;
            }
        }

        if (!$canIDeleteFile) {
            throw new \Exception('Cannot delete this path:' . $pathOrFile . '. Allowed paths are:' . implode(',', $whiteListedPaths));
        }

        $exec = shell_exec('rm -rf ' . $pathOrFile);

        return $exec;
    }

    public static function exec($command, $argsArray = [])
    {
        $args = '';
        if (! empty($argsArray)) {
            foreach ($argsArray as $arg) {
                $args .= escapeshellarg($arg).' ';
            }
        }

        $fullCommand = $command.' '.$args;

        $execOutput = shell_exec($fullCommand);

        return $execOutput;
    }

}
