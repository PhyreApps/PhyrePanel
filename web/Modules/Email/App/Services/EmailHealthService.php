<?php

namespace Modules\Email\App\Services;

class EmailHealthService
{
    public function checkDovecotStatus(): string
    {
        return $this->checkServiceStatus('dovecot');
    }

    public function checkPostfixStatus(): string
    {
        return $this->checkServiceStatus('postfix');
    }

    public function checkOpenDkimStatus(): string
    {
        return $this->checkServiceStatus('opendkim');
    }

    public function checkFirewallStatus(): string
    {
        return shell_exec('sudo ufw status');
    }

    private function checkServiceStatus(string $serviceName): string
    {
        $status = shell_exec("systemctl is-active $serviceName");
        return trim($status) === 'active' ? 'Running' : 'Not Running';
    }
}
