<?php

namespace Modules\Email\App\Filament\Resources\EmailAliasResource\Pages;

use Modules\Email\App\Filament\Resources\EmailAliasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmailAlias extends EditRecord
{
    protected static string $resource = EmailAliasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
