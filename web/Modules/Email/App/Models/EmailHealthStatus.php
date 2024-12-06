<?php

namespace Modules\Email\App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Email\App\Services\EmailHealthService;
use Sushi\Sushi;

class EmailHealthStatus extends Model
{
    use Sushi;

    protected $schema = [
        'service' => 'string',
        'status' => 'string',
    ];

    public function getRows()
    {
        $service = new EmailHealthService();

        return [
            [
                'service' => 'Dovecot',
                'status' => $service->checkDovecotStatus(),
            ],
            [
                'service' => 'Postfix',
                'status' => $service->checkPostfixStatus(),
            ],
            [
                'service' => 'OpenDKIM',
                'status' => $service->checkOpenDkimStatus(),
            ],
            [
                'service' => 'Firewall',
                'status' => $service->checkFirewallStatus(),
            ],
        ];
    }
}
