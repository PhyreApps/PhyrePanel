<?php

namespace Modules\Email\App\Services;

class EmailService
{
    public function restartService(string $serviceName): string
    {
        $output = shell_exec("sudo systemctl restart $serviceName");
        return $output ? trim($output) : "$serviceName restarted successfully.";
    }
    public function stopService(string $serviceName): string
    {
        $output = shell_exec("sudo systemctl stop $serviceName");
        return $output ? trim($output) : "$serviceName stopped successfully.";
    }
    public function startService(string $serviceName): string
    {
        $output = shell_exec("sudo systemctl start $serviceName");
        return $output ? trim($output) : "$serviceName started successfully.";
    }
    public function getLog(string $serviceName): string
    {
        $logFilePath = "/var/log/{$serviceName}.log"; // Adjust the path as needed
        return file_exists($logFilePath) ? file_get_contents($logFilePath) : 'Log file not found.';
    }
}

