<?php

namespace App\Filament\Resources\BackupResource\Pages;

use App\Filament\Resources\BackupResource;
use App\Jobs\RestoreBackup;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
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
            Actions\Action::make('restore')
                ->icon('heroicon-o-cloud-arrow-up')
                ->slideOver()
                ->modalHeading('Restore data from backup file')
                ->form(function () {
                    return [
                        FileUpload::make('backupFile')
                            ->placeholder('Upload your backup file to restore data')
                            ->helperText('Supported file types: .zip')
                            ->disk('local')
                            ->directory('backup-restores')
                            ->visibility('private')
                            ->acceptedFileTypes(['application/zip'])
                    ];
                })->afterFormValidated(function (array $data) {

                    RestoreBackup::dispatch($data['backupFile']);

                }),

            Actions\CreateAction::make()
                ->icon('heroicon-o-clock')
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
