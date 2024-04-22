<?php

namespace App\Filament\Resources\PhyreServerResource\Pages;

use App\Filament\Resources\PhyreServerResource;
use App\Models\PhyreServer;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ManageRecords;

class ListPhyreServers extends ManageRecords
{
    protected static string $resource = PhyreServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Sync Servers Resources')->action(function() {
                $findPhyreServers = PhyreServer::all();
                if ($findPhyreServers->count() > 0) {
                    foreach ($findPhyreServers as $phyreServer) {
                        $phyreServer->syncResources();
                    }
                }
            }),
            Actions\CreateAction::make(),
        ];
    }
}
