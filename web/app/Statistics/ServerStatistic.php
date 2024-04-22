<?php

namespace App\Statistics;

class ServerStatistic
{
    public function getCurrentStats()
    {
        $memory = [
            'total' => 0,
            'used' => 0,
            'free' => 0,
            'shared' => 0,
            'buffCache' => 0,
            'available' => 0,
        ];

        $freeMemoryExec = shell_exec('free -m | grep Mem | awk \'{print $1 " " $2 " " $3 " " $4 " " $5 " " $6 " " $7}\'');
        $freeMemoryExp = explode(' ', $freeMemoryExec);

        if (isset($freeMemoryExp[1])) {
            $memory['total'] = $this->getFormattedFileSize($freeMemoryExp[1] * 1024 * 1024, 2);
            $memory['totalGb'] = $freeMemoryExp[1] * 1024 * 1024;
        }
        if (isset($freeMemoryExp[2])) {
            $memory['used'] = $this->getFormattedFileSize($freeMemoryExp[2] * 1024 * 1024, 2);
            $memory['usedGb'] = $freeMemoryExp[2] * 1024 * 1024;
        }
        if (isset($freeMemoryExp[3])) {
            $memory['free'] = $this->getFormattedFileSize($freeMemoryExp[3] * 1024 * 1024, 2);
            $memory['freeGb'] = $freeMemoryExp[3] * 1024 * 1024;
        }
        if (isset($freeMemoryExp[4])) {
            $memory['shared'] = $this->getFormattedFileSize($freeMemoryExp[4] * 1024 * 1024, 2);
            $memory['sharedGb'] = $freeMemoryExp[4] * 1024 * 1024;
        }
        if (isset($freeMemoryExp[5])) {
            $memory['buffCache'] = $this->getFormattedFileSize($freeMemoryExp[5] * 1024 * 1024, 2);
        }
        if (isset($freeMemoryExp[6])) {
            $memory['available'] = $this->getFormattedFileSize($freeMemoryExp[6] * 1024 * 1024, 2);
            $memory['availableGb'] = $freeMemoryExp[6] * 1024 * 1024;
        }

        $diskMemoryExec = shell_exec('df -h | grep /dev/sda1 | awk \'{print $2 " " $3 " " $4 " " $5 " " $6}\'');
        $diskMemoryExp = explode(' ', $diskMemoryExec);

        $diskMemory = [
            'total' => 0,
            'used' => 0,
            'free' => 0,
            'usedPercentage' => 0,
            'mountedOn' => '',
        ];
        if (isset($diskMemoryExp[0])) {
            $diskMemory['total'] = $diskMemoryExp[0].'B';
        }
        if (isset($diskMemoryExp[1])) {
            $diskMemory['used'] = $diskMemoryExp[1].'B';
        }
        if (isset($diskMemoryExp[2])) {
            $diskMemory['free'] = $diskMemoryExp[2].'B';
        }
        if (isset($diskMemoryExp[3])) {
            $diskMemory['usedPercentage'] = $diskMemoryExp[3];
        }

        $cpuLoad = [
            '1min' => 0,
            '5min' => 0,
            '15min' => 0,
        ];
        $cpuLoadExec = shell_exec('uptime');
        $cpuLoadExp = explode('load average:', $cpuLoadExec);
        if (isset($cpuLoadExp[1])) {
            $loadAverage = explode(',', $cpuLoadExp[1]);
            if (isset($loadAverage[0])) {
                $cpuLoad['1min'] = $loadAverage[0];
            }
            if (isset($loadAverage[1])) {
                $cpuLoad['5min'] = $loadAverage[1];
            }
            if (isset($loadAverage[2])) {
                $cpuLoad['15min'] = $loadAverage[2];
            }
        }

        $totalTasks = shell_exec('ps -e | wc -l');
        $totalTasks = intval($totalTasks);

        $uptime = shell_exec('uptime -p');
        $uptime = str_replace('up ', '', $uptime);

        return [
            'memory' => $memory,
            'disk' => $diskMemory,
            'cpu' => $cpuLoad,
            'totalTasks' => $totalTasks,
            'uptime' => $uptime,
        ];

    }

    public function getFormattedFileSize($size, $precision)
    {
        switch (true) {
            case $size / 1024 < 1:
                return $size.'B';
            case $size / pow(1024, 2) < 1:
                return round($size / 1024, $precision).'KB';
            case $size / pow(1024, 3) < 1:
                return round($size / pow(1024, 2), $precision).'MB';
            case $size / pow(1024, 4) < 1:
                return round($size / pow(1024, 3), $precision).'GB';
            case $size / pow(1024, 5) < 1:
                return round($size / pow(1024, 4), $precision).'TB';
            default:
                return 'Error: invalid input or file is too large.';
        }
    }
}
