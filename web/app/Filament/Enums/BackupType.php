<?php

namespace App\Filament\Enums;

use Filament\Support\Contracts\HasLabel;
use JaOcero\RadioDeck\Contracts\HasDescriptions;
use JaOcero\RadioDeck\Contracts\HasIcons;

enum BackupType: string implements HasLabel, HasDescriptions, HasIcons
{

    case FULL_BACKUP = 'full';
    case SYSTEM_BACKUP = 'system';
    case DATABASE_BACKUP = 'database';
    case HOSTING_SUBSCRIPTION_BACKUP = 'hosting_subscription';
    public function getLabel(): ?string
    {
        return match ($this) {
            self::FULL_BACKUP => 'Full Backup',
            self::SYSTEM_BACKUP => 'System Backup',
            self::DATABASE_BACKUP => 'Database Backup',
            self::HOSTING_SUBSCRIPTION_BACKUP => 'Hosting Subscription Backup',
        };
    }

    public function getDescriptions(): ?string
    {
        return match ($this) {
            self::FULL_BACKUP => 'A full backup is a complete copy of your website files and database. It is the best way to protect your website data.',
            self::SYSTEM_BACKUP => 'A system backup is a copy of your website files and system files. It is useful for restoring your website if there is a problem with the system files.',
            self::DATABASE_BACKUP => 'A database backup is a copy of your website database. It is useful for restoring your website if there is a problem with the database.',
            self::HOSTING_SUBSCRIPTION_BACKUP => 'A hosting subscription backup is a copy of your website files and database. It is useful for restoring your website if there is a problem with the hosting subscription.',
        };
    }

    public function getIcons(): ?string
    {
        return match ($this) {
            self::FULL_BACKUP => 'heroicon-o-inbox-stack',
            self::SYSTEM_BACKUP => 'heroicon-o-cog',
            self::DATABASE_BACKUP => 'phyre-mysql',
            self::HOSTING_SUBSCRIPTION_BACKUP => 'heroicon-o-server',
        };
    }
}
