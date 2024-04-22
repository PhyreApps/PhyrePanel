<?php

namespace Modules\Microweber\Filament\Clusters\Microweber\Resources\InstallationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Microweber\Filament\Admin\Resources\InstallationResource;

class EditInstallation extends EditRecord
{
    protected static string $resource = InstallationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
