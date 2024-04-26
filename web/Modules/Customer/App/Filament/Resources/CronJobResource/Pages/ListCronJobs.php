<?php

namespace Modules\Customer\App\Filament\Resources\CronJobResource\Pages;

use Modules\Customer\App\Filament\Resources\CronJobResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCronJobs extends ListRecords
{
    protected static string $resource = CronJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
