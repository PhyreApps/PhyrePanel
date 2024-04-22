<?php

namespace App\Filament\Resources\HostingSubscriptionResource\Pages;

use App\Filament\Resources\HostingSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHostingSubscription extends EditRecord
{
    protected static string $resource = HostingSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
