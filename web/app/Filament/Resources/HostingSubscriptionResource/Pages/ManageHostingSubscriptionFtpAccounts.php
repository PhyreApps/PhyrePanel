<?php

namespace app\Filament\Resources\HostingSubscriptionResource\Pages;

use App\BackupStorage;
use App\Filament\Enums\BackupStatus;
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
use Illuminate\Support\Carbon;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;

class ManageHostingSubscriptionFtpAccounts extends ManageRelatedRecords
{
    protected static string $resource = HostingSubscriptionResource::class;

    protected static string $relationship = 'ftpAccounts';

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    public function getTitle(): string|Htmlable
    {
        $recordTitle = $this->getRecordTitle();

        $recordTitle = $recordTitle instanceof Htmlable ? $recordTitle->toHtml() : $recordTitle;

        return "Manage {$recordTitle} FTP Accounts";
    }

    public function getBreadcrumb(): string
    {
        return 'FTP Accounts';
    }

    public static function getNavigationLabel(): string
    {
        return 'FTP Accounts';
    }

    public function form(Form $form): Form
    {
        $systemUsername = $this->record->system_username;

        return $form
            ->schema([

                Forms\Components\TextInput::make('username')
                    ->label('Username')
                    ->required(),

                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->required(),
                Forms\Components\TextInput::make('confirm_password')
                    ->label('Confirm Password')
                    ->same('password')
                    ->required(),

                Forms\Components\TextInput::make('path')
                    ->label('Home Directory')
                    ->prefix('/home/' . $systemUsername . '/')
                    ->required(),

                Forms\Components\TextInput::make('quota')
                    ->label('Quota'),

                Forms\Components\Toggle::make('unlimited_quota')
                    ->label('Unlimited Quota')
                    ->default(false),

            ])
            ->columns(1);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(1)
            ->schema([
                //TextEntry::make('id')->label('id'),
                TextEntry::make('username')->label('Username'),
                TextEntry::make('path')->label('Home Directory'),
                TextEntry::make('quota')->label('Quota'),
                IconEntry::make('unlimited_quota')->label('Unlimited Quota'),

            ]);
    }

    public function table(Table $table): Table
    {

        return $table
            ->recordTitleAttribute('username')
            ->columns([

                Tables\Columns\TextColumn::make('username')
                    ->label('Username'),

                Tables\Columns\TextColumn::make('path')
                    ->label('Home Directory')
                ->default('/'),


            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
//
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
