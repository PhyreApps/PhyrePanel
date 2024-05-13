<?php

namespace Modules\Minecraft\App\Filament\Resources\MinecraftServerResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use Modules\Minecraft\App\Filament\Resources\MinecraftServerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class ViewMinecraftServer extends ViewRecord
{
    protected static string $resource = MinecraftServerResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
