<?php

namespace App\Filament\Enums;

use Filament\Support\Contracts\HasLabel;
use JaOcero\RadioDeck\Contracts\HasDescriptions;
use JaOcero\RadioDeck\Contracts\HasIcons;

enum HostingSubscriptionBackupType: string implements HasLabel, HasDescriptions, HasIcons
{
    case FULL_BACKUP = 'full';
    case DATABASE_BACKUP = 'database';
    public function getLabel(): ?string
    {
        return match ($this) {
            self::FULL_BACKUP => 'Full Backup',
            self::DATABASE_BACKUP => 'Database',
        };
    }

    public function getDescriptions(): ?string
    {
        return match ($this) {
            self::FULL_BACKUP => 'A full backup of the hosting subscription. Includes all files and databases.',
            self::DATABASE_BACKUP => 'A backup of the hosting subscription database',
        };
    }

    public function getIcons(): ?string
    {
        return match ($this) {
            self::FULL_BACKUP => 'heroicon-o-inbox-stack',
            self::DATABASE_BACKUP => 'phyre-mysql',
        };
    }
}
