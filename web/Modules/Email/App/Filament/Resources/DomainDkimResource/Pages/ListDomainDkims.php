<?php

namespace Modules\Email\App\Filament\Resources\DomainDkimResource\Pages;

use Modules\Email\App\Filament\Resources\DomainDkimResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDomainDkims extends ListRecords
{
    protected static string $resource = DomainDkimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
