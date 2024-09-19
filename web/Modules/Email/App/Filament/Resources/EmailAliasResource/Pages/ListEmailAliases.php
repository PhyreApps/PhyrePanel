<?php

namespace Modules\Email\App\Filament\Resources\EmailAliasResource\Pages;

use Modules\Email\App\Filament\Resources\EmailAliasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmailAliases extends ListRecords
{
    protected static string $resource = EmailAliasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
