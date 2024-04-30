<?php

namespace App;

class ShellApi
{
    public function safeRmRf($pathOrFile, $whiteListedPaths = [])
    {
        if (in_array($pathOrFile, $whiteListedPaths)) {
            return false;
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
