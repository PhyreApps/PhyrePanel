<?php

namespace Modules\Email\App\Filament\Resources\EmailAliasDomainResource\Pages;

use Modules\Email\App\Filament\Resources\EmailAliasDomainResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmailAliasDomains extends ListRecords
{
    protected static string $resource = EmailAliasDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
