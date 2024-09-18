<?php

namespace Modules\Email\App\Filament\Resources\EmailBoxResource\Pages;

use Modules\Email\App\Filament\Resources\EmailBoxResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmailBoxes extends ListRecords
{
    protected static string $resource = EmailBoxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
