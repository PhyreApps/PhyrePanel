<?php

namespace Modules\Minecraft\App\Filament\Resources\MinecraftServerResource\Pages;

use Modules\Minecraft\App\Filament\Resources\MinecraftServerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMinecraftServer extends EditRecord
{
    protected static string $resource = MinecraftServerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
