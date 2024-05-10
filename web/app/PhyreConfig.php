<?php

namespace App;

class PhyreConfig
{
    public static function get($key, $default = null)
    {
        // Parse without sections
        $iniArray = parse_ini_file(base_path() . "/phyre-config.ini");
        if (isset($iniArray[$key])) {
            return $iniArray[$key];
        }

        return $default;
    }

}
