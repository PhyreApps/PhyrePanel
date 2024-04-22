<?php

namespace App\Filament\Resources\HostingPlanResource\Pages;

use App\Filament\Resources\HostingPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHostingPlan extends EditRecord
{
    protected static string $resource = HostingPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
