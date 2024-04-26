<?php

namespace Modules\Customer\App\Filament\Resources\DomainResource\Pages;

use Modules\Customer\App\Filament\Resources\DomainResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDomain extends CreateRecord
{
    protected static string $resource = DomainResource::class;
}
