<?php

namespace App\Filament\Resources\PhyreServerResource\Pages;

use App\Filament\Resources\PhyreServerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPhyreServer extends EditRecord
{
    protected static string $resource = PhyreServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
