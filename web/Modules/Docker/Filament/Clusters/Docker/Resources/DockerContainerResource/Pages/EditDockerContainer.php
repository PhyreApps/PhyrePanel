<?php
namespace Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource;

class EditDockerContainer extends EditRecord
{
    protected static string $resource = DockerContainerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
