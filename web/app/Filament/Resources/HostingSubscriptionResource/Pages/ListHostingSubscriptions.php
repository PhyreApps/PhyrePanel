<?php

namespace App\Filament\Resources\HostingSubscriptionResource\Pages;

use App\Filament\Resources\HostingSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHostingSubscriptions extends ListRecords
{
    protected static string $resource = HostingSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
