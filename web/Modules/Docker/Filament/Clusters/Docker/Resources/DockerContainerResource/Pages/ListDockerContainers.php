<?php
namespace Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource\Pages;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource;

class ListDockerContainers extends ListRecords
{
    protected static string $resource = DockerContainerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
