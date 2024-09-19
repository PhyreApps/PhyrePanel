<?php

namespace Modules\Email\App\Filament\Resources\EmailAliasDomainResource\Pages;

use Modules\Email\App\Filament\Resources\EmailAliasDomainResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmailAliasDomain extends EditRecord
{
    protected static string $resource = EmailAliasDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
