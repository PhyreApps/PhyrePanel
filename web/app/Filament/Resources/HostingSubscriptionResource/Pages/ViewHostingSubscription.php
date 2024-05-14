<?php

namespace App\Filament\Resources\HostingSubscriptionResource\Pages;

use App\Filament\Resources\HostingSubscriptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\ViewRecord;

class ViewHostingSubscription extends ViewRecord
{
    protected static string $resource = HostingSubscriptionResource::class;

    protected static string $view = 'filament.pages.view-hosting-subscription';

    public static function getPages(): array
    {
        return [

        ];
    }

    protected function getHeaderActions(): array
    {
        return [

        ];
    }

}
