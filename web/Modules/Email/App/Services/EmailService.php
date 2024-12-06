<?php

namespace Modules\Email\App\Services;

class EmailService
{

    public static $services = [
        [
            'service' => 'postfix',
            'logPath' => '/var/log/mail.log',
        ],
        [
            'service' => 'dovecot',
            'logPath' => '/var/log/dovecot.log',
        ],
        [
            'service'=>'opendkim',
            'logPath'=>'/var/log/mail.log',
        ],
        [
            'service' => 'firewalld',
            'logPath' => '/var/log/firewalld',
        ],
        [
            'service' => 'syslog',
            'logPath' => '/var/log/syslog',
        ]
    ];

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
        $service = collect(self::$services)->firstWhere('service', $serviceName);

        if ($service && file_exists($service['logPath'])) {
            return file_get_contents($service['logPath']);
        }

        return 'Log file not found.';
    }
    public function truncateLog(string $serviceName): string
    {
        $service = collect(self::$services)->firstWhere('service', $serviceName);

        if ($service && file_exists($service['logPath'])) {
            file_put_contents($service['logPath'], '');
            return 'Log file truncated.';
        }

        return 'Log file not found.';
    }
}

