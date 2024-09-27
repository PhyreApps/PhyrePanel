<?php

namespace Modules\Email\App\Filament\Resources\DomainsResource\Pages;

use Modules\Email\App\Filament\Resources\EmailDomainResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDomains extends ListRecords
{
    protected static string $resource = EmailDomainResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
