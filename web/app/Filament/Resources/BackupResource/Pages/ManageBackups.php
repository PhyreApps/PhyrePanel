<?php

namespace App\Filament\Resources\BackupResource\Pages;

use App\Filament\Resources\BackupResource;
use App\Jobs\RestoreBackup;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\DB;

class ManageBackups extends ManageRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = BackupResource::class;

    protected function getActions(): array
    {
        $restoringBackup = false;
        $checkJob = DB::table('jobs')->where('payload', 'like', '%RestoreBackup%')->first();
        if ($checkJob) {
            $restoringBackup = true;
        }

        return [


            Actions\Action::make('restore')
                ->hidden($restoringBackup)
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


            Actions\Action::make('settings')
                ->icon('heroicon-o-cog')
                ->slideOver()
                ->modalHeading('Backup Settings')
                ->form(function () {
                    return [
                        Select::make('backup_frequency')
                            ->options([
                                'daily' => 'Daily',
                                'weekly' => 'Weekly',
                                'monthly' => 'Monthly',
                            ])
                            ->default(setting('backup_frequency', 'daily'))
                            ->required()
                            ->columnSpanFull(),

                        Select::make('backup_retention_days')
                            ->options([
                                '1' => '1 Day',
                                '7' => '1 Week',
                                '30' => '1 Month',
                                '90' => '3 Months',
                                '180' => '6 Months',
                                '365' => '1 Year',
                            ])
                            ->default(setting('backup_retention_days', '7'))
                            ->required()
                            ->columnSpanFull(),
                    ];
                })->afterFormValidated(function (array $data) {

                    // Update backup settings
                    setting($data);

                }),
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
