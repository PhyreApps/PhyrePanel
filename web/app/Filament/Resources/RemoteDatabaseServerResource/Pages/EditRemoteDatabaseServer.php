<?php

namespace App\Filament\Resources\RemoteDatabaseServerResource\Pages;

use App\Filament\Resources\RemoteDatabaseServerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRemoteDatabaseServer extends EditRecord
{
    protected static string $resource = RemoteDatabaseServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
