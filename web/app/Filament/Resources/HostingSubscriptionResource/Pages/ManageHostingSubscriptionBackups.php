<?php

namespace app\Filament\Resources\HostingSubscriptionResource\Pages;

use App\Filament\Enums\HostingSubscriptionBackupType;
use App\Filament\Resources\Blog\PostResource;
use App\Filament\Resources\HostingSubscriptionResource;
use App\Models\Backup;
use App\Models\DatabaseUser;
use App\Models\HostingSubscriptionBackup;
use App\Models\RemoteDatabaseServer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
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
        return 'Manage Backups';
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
                TextEntry::make('id')->label('id'),
            ]);
    }

    public function table(Table $table): Table
    {

        return $table
            ->recordTitleAttribute('id')
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
                        return $backup->size ? $backup->size : 'N/A';
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
               // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
