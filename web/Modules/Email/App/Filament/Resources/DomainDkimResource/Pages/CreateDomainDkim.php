<?php

namespace Modules\Email\App\Filament\Resources\DomainDkimResource\Pages;

use Modules\Email\App\Filament\Resources\DomainDkimResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDomainDkim extends CreateRecord
{
    protected static string $resource = DomainDkimResource::class;
}
