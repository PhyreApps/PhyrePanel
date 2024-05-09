<?php

namespace App\Filament\Resources\BackupResource\Pages;

use App\Filament\Resources\BackupResource;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;

class ManageBackups extends ManageRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = BackupResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->size('sm')
                ->slideOver(),
        ];
    }


    protected function getHeaderWidgets(): array
    {
        return BackupResource::getWidgets();
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'completed' => Tab::make()->query(fn ($query) => $query->where('status', 'completed')),
            'processing' => Tab::make()->query(fn ($query) => $query->where('status', 'processing')),
            'failed' => Tab::make()->query(fn ($query) => $query->where('status', 'failed')),
        ];
    }
}
