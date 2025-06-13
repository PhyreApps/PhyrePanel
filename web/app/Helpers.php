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

    /**
     * Format bytes to human readable format
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    /**
     * Check if a string is a valid domain
     */
    public static function isValidDomain(string $domain): bool
    {
        return filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) !== false;
    }
    
    /**
     * Check if a string is a valid IP address
     */
    public static function isValidIp(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP) !== false;
    }
    
    /**
     * Parse Apache configuration to get current ports
     */
    public static function getApachePorts(): array
    {
        $ports = [];
        $configFiles = [
            '/etc/apache2/ports.conf',
            '/etc/httpd/conf/httpd.conf',
            '/usr/local/apache2/conf/httpd.conf'
        ];
        
        foreach ($configFiles as $configFile) {
            if (file_exists($configFile)) {
                $content = file_get_contents($configFile);
                
                // Look for Listen directives
                if (preg_match_all('/^\s*Listen\s+(?:\*:)?(\d+)/m', $content, $matches)) {
                    $ports = array_merge($ports, $matches[1]);
                }
                break;
            }
        }
        
        return array_unique($ports);
    }
    
    /**
     * Check if Apache SSL module is enabled
     */
    public static function isApacheSslEnabled(): bool
    {
        // Check common SSL module files
        $sslFiles = [
            '/etc/apache2/mods-enabled/ssl.load',
            '/etc/httpd/conf.modules.d/00-ssl.conf'
        ];
        
        foreach ($sslFiles as $file) {
            if (file_exists($file)) {
                return true;
            }
        }
        
        // Check if SSL is compiled in
        $output = shell_exec('httpd -M 2>/dev/null | grep ssl || apache2 -M 2>/dev/null | grep ssl');
        return !empty($output);
    }

}
