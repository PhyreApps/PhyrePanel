<?php

namespace app\Filament\Resources\HostingSubscriptionResource\Pages;

use App\BackupStorage;
use App\Filament\Enums\BackupStatus;
use App\Filament\Enums\HostingSubscriptionBackupType;
use App\Filament\Resources\Blog\PostResource;
use App\Filament\Resources\HostingSubscriptionResource;
use App\Helpers;
use App\Models\Backup;
use App\Models\DatabaseUser;
use App\Models\HostingSubscriptionBackup;
use App\Models\RemoteDatabaseServer;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;

class ManageHostingSubscriptionBackups extends ManageRelatedRecords
{
    protected static string $resource = HostingSubscriptionResource::class;

    protected static string $relationship = 'backups';

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    public function getTitle(): string | Htmlable
    {
        $recordTitle = $this->getRecordTitle();

        $recordTitle = $recordTitle instanceof Htmlable ? $recordTitle->toHtml() : $recordTitle;

        return "Manage {$recordTitle} Backups";
    }

    public function getBreadcrumb(): string
    {
        return 'Backups';
    }

    public static function getNavigationLabel(): string
    {
        return 'Backups';
    }

    public function form(Form $form): Form
    {

        return $form
            ->schema([

                RadioDeck::make('backup_type')
                    ->live()
                  //  ->default('full')
                    ->options(HostingSubscriptionBackupType::class)
                    ->icons(HostingSubscriptionBackupType::class)
                    ->descriptions(HostingSubscriptionBackupType::class)
                    ->required()
                    ->color('primary')
                    ->columnSpanFull(),


            ])
            ->columns(1);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(1)
            ->schema([
                //TextEntry::make('id')->label('id'),
                TextEntry::make('backup_type')->label('Backup Type'),
                TextEntry::make('status')->label('Status'),
                TextEntry::make('completed_at')->label('Completed At'),
                TextEntry::make('size')->label('Size'),
                TextEntry::make('path')->label('Path'),
                TextEntry::make('filepath')->label('File Path'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_name')
            ->columns([

                Tables\Columns\TextColumn::make('backup_type')
                    ->state(function (HostingSubscriptionBackup $backup) {
                        return ucfirst($backup->backup_type);
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->badge(),

                Tables\Columns\TextColumn::make('completed_at')
                    ->state(function (HostingSubscriptionBackup $backup) {
                        return $backup->completed_at ? $backup->completed_at : 'N/A';
                    }),

                Tables\Columns\TextColumn::make('size')
                    ->state(function (HostingSubscriptionBackup $backup) {
                        return ($backup->size > 0) ? Helpers::getHumanReadableSize($backup->size) : 'N/A';
                    }),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
//
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->hidden(function (HostingSubscriptionBackup $backup) {
                        return $backup->status !== BackupStatus::Completed;
                    })
                    ->action(function (HostingSubscriptionBackup $backup) {

                        $backupStorage = BackupStorage::getInstance($backup->root_path);
                        $tempUrl = $backupStorage->temporaryUrl($backup->file_name, Carbon::now()->addMinutes(5));

                        return redirect($tempUrl);
                    }),


                Tables\Actions\Action::make('viewLog')
                    ->label('View Log')
                    ->icon('heroicon-o-document')
                    ->hidden(function (HostingSubscriptionBackup $backup) {
                        $hide = true;
                        if ($backup->status === BackupStatus::Processing || $backup->status === BackupStatus::Failed) {
                            $hide = false;
                        }
                        return $hide;
                    })
                    ->modalContent(function (HostingSubscriptionBackup $backup) {
                        return view('filament.modals.view-livewire-component', [
                            'component' => 'hosting-subscription-backup-log',
                            'componentProps' => [
                                'hostingSubscriptionBackupId' => $backup->id,
                            ],
                        ]);
                    }),

                Tables\Actions\ViewAction::make(),
             //   Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
