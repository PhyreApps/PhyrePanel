<?php

namespace Modules\Customer\App\Filament\Resources\DatabaseResource\Pages;

use Modules\Customer\App\Filament\Resources\DatabaseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDatabase extends CreateRecord
{
    protected static string $resource = DatabaseResource::class;
}
