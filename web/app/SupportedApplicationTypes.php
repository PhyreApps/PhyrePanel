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

    public static function getPHPVersions()
    {
        $versions = [];
        $phpVersions = [
            '5.6',
            '7.4',
            '8.0',
            '8.1',
            '8.2',
            '8.3',
        ];
        foreach ($phpVersions as $version) {
            $versions[$version] = 'PHP '.$version;
        }
        return $versions;
    }

    public static function getPHPModules()
    {
        $modules = [];
        $phpModules = [
            'bcmath' => 'BCMath',
            'bz2' => 'Bzip2',
            'calendar' => 'Calendar',
            'ctype' => 'Ctype',
            'curl' => 'Curl',
            'dom' => 'DOM',
            'fileinfo' => 'Fileinfo',
            'gd' => 'GD',
            'intl' => 'Intl',
            'mbstring' => 'Mbstring',
            'mysql' => 'MySQL',
            'opcache' => 'OPcache',
            'sqlite3' => 'SQLite3',
            'xmlrpc' => 'XML-RPC',
            'zip' => 'Zip',
        ];
        foreach ($phpModules as $module => $name) {
            $modules[$module] = $name;
        }
        return $modules;
    }

}
