<?php

namespace Modules\Docker\Filament\Clusters\Docker\Resources\DockerTemplateResource\Pages;

use Modules\Docker\Filament\Clusters\Docker\Resources\DockerTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDockerTemplate extends EditRecord
{
    protected static string $resource = DockerTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
