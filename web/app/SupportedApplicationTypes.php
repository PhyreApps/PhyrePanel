<?php

namespace App;

class SupportedApplicationTypes
{
    public static function getNodeJsVersions()
    {
        $versions = [];
        $nodeJsVersions = [
            '14',
            '16',
            '17',
            '18',
            '19',
            '20',
        ];
        foreach ($nodeJsVersions as $version) {
            $versions[$version] = 'Node.js '.$version;
        }
        return $versions;
    }

    public static function getRubyVersions()
    {
        $versions = [];
        $rubyVersions = [
            '2.7',
            '3.0',
            '3.1',
            '3.2',
            '3.3',
            '3.4',
        ];
        foreach ($rubyVersions as $version) {
            $versions[$version] = 'Ruby '.$version;
        }
        return $versions;
    }

    public static function getPythonVersions()
    {
        $versions = [];
        $pythonVersions = [
            '2.7',
            '3.6',
            '3.7',
            '3.8',
            '3.9',
            '3.10',
        ];
        foreach ($pythonVersions as $version) {
            $versions[$version] = 'Python '.$version;
        }
        return $versions;
    }


    public static function getInstalledPHPModules($phpVersion)
    {
        $modules = [];
        $getModules = shell_exec('php' . $phpVersion . ' -m');
        if (!empty($getModules)) {
            $getModules = explode("\n", $getModules);
            if (is_array($getModules)) {
                $getModules = array_filter($getModules);
                foreach ($getModules as $module) {
                    if ($module == '[PHP Modules]') {
                        continue;
                    }
                    if ($module == '[Zend Modules]') {
                        continue;
                    }
                    $modules[] = $module;
                }
                $modules = array_unique($modules);
            }
        }
        return $modules;
    }

    public static function getInstalledPHPVersions()
    {
        $installedPHPVersions = [];

        $getPHPVersions = shell_exec('sudo update-alternatives --list php');
        if (!empty($getPHPVersions)) {
            $getPHPVersions = explode("\n", $getPHPVersions);
            if (is_array($getPHPVersions)) {
                $getPHPVersions = array_filter($getPHPVersions);
                foreach ($getPHPVersions as $phpVersion) {
                    $phpVersion = str_replace('/usr/bin/php', '', $phpVersion);
                    $phpVersion = str_replace('php', '', $phpVersion);
                    $phpVersion = str_replace('.', '', $phpVersion);
                    $phpVersion = substr($phpVersion, 0, 1) . '.' . substr($phpVersion, 1);
                    $installedPHPVersions[] = [
                        'version' => $phpVersion,
                        'modules' => self::getInstalledPHPModules($phpVersion) ?? 'No modules found.',
                    ];
                }
            }
        }

        return $installedPHPVersions;

    }
    public static function getPHPVersions()
    {
        $versions = [];
        $phpVersions = [];

        $getPHPVersions = shell_exec('apt-cache search php | grep php[0-9] | cut -d" " -f1');
        if (!empty($getPHPVersions)) {
            $getPHPVersions = explode("\n", $getPHPVersions);
            foreach ($getPHPVersions as $version) {
                $regex = '/php[0-9]+\.[0-9]+/';
                preg_match($regex, $version, $pregMatch);
                if (!isset($pregMatch[0])) {
                    continue;
                }
                $phpVersion = $pregMatch[0];
                $phpVersion = str_replace('php', '', $phpVersion);

                $phpVersions[$phpVersion] = $phpVersion;
            }
        }

        foreach ($phpVersions as $version) {
            $versions[$version] = 'PHP '.$version;
        }

        return $versions;
    }

    public static function getPHPModules($filters = [])
    {
        $modules = [];
        $phpModules = [];

        $allowedModules = [
            'gd',
            'imagick',
            'intl',
            'mbstring',
            'mysqli',
            'pdo',
            'pdo_mysql',
            'pdo_pgsql',
            'pgsql',
            'soap',
            'xml',
            'zip',
            'bcmath',
            'calendar',
            'exif',
            'ftp',
            'gettext',
            'iconv',
            'json',
            'ldap',
            'opcache',
            'pcntl',
            'sqlite3',
            'sqlite',
            'soap',
            'mysql',
            'mcrypt',
            'curl',
            'mbstring',
            'intl',
            'gd'
        ];

        $getModules = shell_exec('apt-cache search php | grep php- | cut -d" " -f1');
        if (!empty($getModules)) {
            $getModules = explode("\n", $getModules);
            foreach ($getModules as $module) {
                $module = str_replace('php-', '', $module);
                if (empty($module)) {
                    continue;
                }
                if (isset($filters['skip'])) {
                    foreach ($filters['skip'] as $skip) {
                        if (fnmatch($skip, $module)) {
                            continue 2;
                        }
                    }
                }
                $phpModules[$module] = ucwords(str_replace('-', ' ', $module));
            }
        }

        foreach ($phpModules as $module => $name) {

            if (!empty($allowedModules) && !in_array($module, $allowedModules)) {
                continue;
            }

            $modules[$module] = $name;
        }
        return $modules;
    }

}
