<?php

namespace App\Filament\Resources\RemoteBackupServerResource\Pages;

use App\Filament\Resources\RemoteBackupServerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRemoteBackupServer extends EditRecord
{
    protected static string $resource = RemoteBackupServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
