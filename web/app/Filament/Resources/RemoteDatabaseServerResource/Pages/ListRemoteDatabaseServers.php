<?php

namespace App\Filament\Resources\RemoteDatabaseServerResource\Pages;

use App\Filament\Resources\RemoteDatabaseServerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRemoteDatabaseServers extends ListRecords
{
    protected static string $resource = RemoteDatabaseServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
