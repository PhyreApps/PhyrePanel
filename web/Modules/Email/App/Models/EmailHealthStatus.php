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
                'status' => ServiceStatus::from($service->checkServiceStatus('dovecot')),
            ],
            [
                'service' => 'Postfix',
                'status' => ServiceStatus::from($service->checkServiceStatus('postfix')),
            ],
            [
                'service' => 'OpenDKIM',
                'status' => ServiceStatus::from($service->checkServiceStatus('opendkim')),
            ],
            [
                'service' => 'Firewall',
                'status' => ServiceStatus::from($service->checkServiceStatus('firewalld')),
            ],
        ];

        return $rows;
    }
}
