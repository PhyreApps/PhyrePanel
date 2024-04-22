<?php
namespace Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource;

class CreateDockerContainer extends CreateRecord
{
    protected static string $resource = DockerContainerResource::class;
}
