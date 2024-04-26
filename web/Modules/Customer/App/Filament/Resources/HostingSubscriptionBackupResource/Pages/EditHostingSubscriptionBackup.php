<?php

namespace Modules\Customer\App\Filament\Resources\HostingSubscriptionBackupResource\Pages;

use Modules\Customer\App\Filament\Resources\HostingSubscriptionBackupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHostingSubscriptionBackup extends EditRecord
{
    protected static string $resource = HostingSubscriptionBackupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
