<?php

namespace Modules\Customer\App\Filament\Resources\CronJobResource\Pages;

use Modules\Customer\App\Filament\Resources\CronJobResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCronJob extends CreateRecord
{
    protected static string $resource = CronJobResource::class;
}
