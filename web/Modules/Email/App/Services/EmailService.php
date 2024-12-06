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
}

