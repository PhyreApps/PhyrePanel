<?php

namespace Modules\Email\App\Filament\Resources\DomainDkimSigningResource\Pages;

use Modules\Email\App\Filament\Resources\DomainDkimSigningResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDomainDkimSigning extends EditRecord
{
    protected static string $resource = DomainDkimSigningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
