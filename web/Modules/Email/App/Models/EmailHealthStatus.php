<?php

namespace Modules\Email\App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Email\App\Enums\ServiceStatus;
use Modules\Email\App\Services\EmailHealthService;
use Modules\Email\App\Services\EmailService;
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

        $rows = [];
        foreach (EmailService::$services as $serviceInfo) {
            $serviceName = $serviceInfo['service'];
            $rows[] = [
                'service' => ucfirst($serviceName),
                'status' => ServiceStatus::from($service->checkServiceStatus($serviceName)),
            ];
        }

        return $rows;
    }
}
