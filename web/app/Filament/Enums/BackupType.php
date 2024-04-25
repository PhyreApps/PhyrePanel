<?php

namespace App\Filament\Enums;

use Filament\Support\Contracts\HasLabel;
use JaOcero\RadioDeck\Contracts\HasDescriptions;
use JaOcero\RadioDeck\Contracts\HasIcons;

enum BackupType: string implements HasLabel, HasDescriptions, HasIcons
{

    case FULL_BACKUP = 'full';
    case SYSTEM_BACKUP = 'system';
    //case HOSTING_SUBSCRIPTION_BACKUP = 'hosting_subscription';
    public function getLabel(): ?string
    {
        return match ($this) {
            self::FULL_BACKUP => 'Full Backup',
            self::SYSTEM_BACKUP => 'System',
          //  self::HOSTING_SUBSCRIPTION_BACKUP => 'Hosting Subscription Backup',
        };
    }

    public function getDescriptions(): ?string
    {
        return match ($this) {
            self::FULL_BACKUP => 'A full backup of the server. Includes phyre system full configuration and hosting subscriptions.',
            self::SYSTEM_BACKUP => 'A backup of the phyre system full configuration',
       //     self::HOSTING_SUBSCRIPTION_BACKUP => 'A backup of a hosting subscription',
        };
    }

    public function getIcons(): ?string
    {
        return match ($this) {
            self::FULL_BACKUP => 'heroicon-o-inbox-stack',
            self::SYSTEM_BACKUP => 'heroicon-o-cog',
           // self::HOSTING_SUBSCRIPTION_BACKUP => 'heroicon-o-server',
        };
    }
}
