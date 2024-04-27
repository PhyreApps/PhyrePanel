<?php

namespace Modules\Docker\Filament\Clusters\Docker\Resources\DockerTemplateResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerTemplateResource;

class ListDockerTemplates extends ListRecords
{
    protected static string $resource = DockerTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
