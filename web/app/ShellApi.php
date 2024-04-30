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

        $errorsBag = [];
        foreach ($whiteListedPaths as $whiteListedPath) {
            if (!Str::of($pathOrFile)->startsWith($whiteListedPath)) {
                $errorsBag[] = 'Cannot delete this path';
            }
        }
        if (!empty($errorsBag)) {
            throw new \Exception('Cannot delete this path');
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
