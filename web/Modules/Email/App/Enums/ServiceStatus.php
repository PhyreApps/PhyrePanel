<?php

namespace Modules\Email\App\Enums;

enum ServiceStatus: string
{
    case ACTIVE = 'Active';
    case RUNNING = 'Running';
    case NOT_RUNNING = 'NotRunning';

    case INACTIVE = 'Inactive';

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
            self::RUNNING => 'success',
            self::NOT_RUNNING => 'danger',
        };
    }
}
