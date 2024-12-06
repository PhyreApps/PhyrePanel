<?php

namespace Modules\Email\App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Email\App\Enums\ServiceStatus;
use Modules\Email\App\Services\EmailHealthService;
use Sushi\Sushi;

class EmailHealthStatus extends Model
{
    use Sushi;

    protected $schema = [
        'service' => 'string',
        'status' => 'string',
    ];

    protected $casts = [
        'status' => ServiceStatus::class,
    ];

    public function getRows()
    {
        $service = new EmailHealthService();

        $rows = [
            [
                'service' => 'Dovecot',
                'status' => ServiceStatus::from($service->checkDovecotStatus()),
            ],
            [
                'service' => 'Postfix',
                'status' => ServiceStatus::from($service->checkPostfixStatus()),
            ],
            [
                'service' => 'OpenDKIM',
                'status' => ServiceStatus::from($service->checkOpenDkimStatus()),
            ],
            [
                'service' => 'Firewall',
                'status' => ServiceStatus::from($service->checkFirewallStatus()),
            ],
        ];

        return $rows;
    }
}
