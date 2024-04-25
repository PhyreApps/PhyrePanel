<?php

namespace App;

class Helpers
{

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

}
