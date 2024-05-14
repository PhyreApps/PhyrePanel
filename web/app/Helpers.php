<?php

namespace App;

class Helpers
{
    public static function extractZip($tarFile, $extractPath)
    {
        shell_exec('mkdir -p ' . $extractPath);

        $exec = shell_exec('unzip -o ' . $tarFile . ' -d ' . $extractPath);

        return $exec;
    }

    public static function extractTar($tarFile, $extractPath)
    {
        shell_exec('mkdir -p ' . $extractPath);

        $exec = shell_exec('tar -xvf ' . $tarFile . ' -C ' . $extractPath);

        return $exec;
    }

    public static function checkPathSize($path)
    {
        // Check path size
        $pathSize = shell_exec('du -sh ' . $path);
        $pathSize = trim($pathSize);
        $pathSize = explode("\t", $pathSize);

        if (isset($pathSize[0])) {
            $pathSize = $pathSize[0];
            return $pathSize;
        }

        return 0;
    }

    public static function getHumanReadableSize($size, $unit = null, $decemals = 2) {

        $size = intval($size);

        $byteUnits = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        if (!is_null($unit) && !in_array($unit, $byteUnits)) {
            $unit = null;
        }

        $extent = 1;
        foreach ($byteUnits as $rank) {
            if ((is_null($unit) && ($size < $extent <<= 10)) || ($rank == $unit)) {
                break;
            }
        }

        return number_format($size / ($extent >> 10), $decemals) . $rank;
    }

}
