<?php

namespace Modules\Email\App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ServiceStatus: string implements HasLabel, HasColor
{
    case ACTIVE = 'Active';
    case RUNNING = 'Running';
    case NOT_RUNNING = 'NotRunning';

    case INACTIVE = 'Inactive';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::RUNNING => 'Running',
            self::NOT_RUNNING => 'Not Running',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
            self::RUNNING => 'success',
            self::NOT_RUNNING => 'danger',
        };
    }
}
