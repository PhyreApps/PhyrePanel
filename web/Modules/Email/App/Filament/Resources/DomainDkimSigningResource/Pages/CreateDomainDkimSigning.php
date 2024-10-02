<?php

namespace Modules\Email\App\Filament\Resources\DomainDkimSigningResource\Pages;

use Modules\Email\App\Filament\Resources\DomainDkimSigningResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDomainDkimSigning extends CreateRecord
{
    protected static string $resource = DomainDkimSigningResource::class;
}
