<?php

namespace Modules\Customer\App\Filament\Resources\CronJobResource\Pages;

use Modules\Customer\App\Filament\Resources\CronJobResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCronJob extends EditRecord
{
    protected static string $resource = CronJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
