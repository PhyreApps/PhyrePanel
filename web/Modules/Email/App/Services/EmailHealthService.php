<?php

namespace Modules\Email\App\Services;

class EmailHealthService
{
    public function checkServiceStatus(string $serviceName): string
    {
        $status = shell_exec("systemctl is-active $serviceName");
        return trim($status) === 'active' ? 'Running' : 'NotRunning';
    }
}
