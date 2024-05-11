<?php

namespace App;

class PhyreConfig
{
    public static function get($key, $default = null)
    {
        // Parse without sections
        $configIni = base_path() . "/phyre-config.ini";
        if (file_exists($configIni)) {
            $iniArray = parse_ini_file($configIni);
            if (isset($iniArray[$key])) {
                return $iniArray[$key];
            }
        }

        return $default;
    }

    public static function getAll()
    {
        // Parse without sections
        $configIni = base_path() . "/phyre-config.ini";
        if (file_exists($configIni)) {
            $iniArray = parse_ini_file($configIni);
            return $iniArray;
        }

        return [];
    }
}
