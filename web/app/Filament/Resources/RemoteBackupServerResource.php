<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RemoteBackupServerResource\Pages;
use App\Filament\Resources\RemoteBackupServerResource\RelationManagers;
use App\Models\RemoteBackupServer;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RemoteBackupServerResource extends Resource
{
    protected static ?string $model = RemoteBackupServer::class;

    protected static ?string $navigationIcon = 'phyre-backup-remote';

    protected static ?string $navigationGroup = 'Server Clustering';

    protected static ?string $navigationLabel = 'Backup Servers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->helperText('A unique name for the backup server')
                            ->required(),
                    ])
                    ->compact(),

                Forms\Components\Section::make('Connection Settings')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Type')
                                    ->helperText('Choose the type of backup server')
                                    ->options([
                                        'ftp' => 'FTP',
                                        'sftp' => 'SFTP',
                                    ])
                                    ->default('ftp')
                                    ->required(),

                                Forms\Components\TextInput::make('hostname')
                                    ->helperText('The hostname or IP address')
                                    ->label('Hostname')
                                    ->required(),

                                Forms\Components\TextInput::make('port')
                                    ->label('Port')
                                    ->default('21')
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(65535)
                                    ->required()
                                    ->helperText('FTP: 21, SFTP: 22'),
                            ]),
                    ])
                    ->compact(),

                Forms\Components\Section::make('Authentication')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('username')
                                    ->label('Username')
                                    ->required(),

                                Forms\Components\TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->required()
                                    ->dehydrated(fn ($state) => filled($state)),

                                    Actions::make([
                                        Forms\Components\Actions\Action::make('test_connection')
                                        ->label('Test Connection')
                                        ->icon('heroicon-m-signal')
                                        ->action(function ($livewire) {
                                            $data = $livewire->form->getState();
                                            
                                            $server = new RemoteBackupServer($data);
                                            $server->healthCheck(); 
                                            
                                            if ($server->status === 'online') {
                                                Notification::make()
                                                    ->title('Connection successful')
                                                    ->success()
                                                    ->send();
                                            } else {
                                                Notification::make()
                                                    ->title('Connection failed')
                                                    ->danger()
                                                    ->send();
                                            }
                                        }),
                                    ])
                            ]),
                    ])
                    ->compact(),

                Forms\Components\Section::make('Storage Settings')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('path')
                            ->label('Path')
                            ->default('/')
                            ->required()
                            ->placeholder('/path/to/backups')
                            ->helperText('Directory path for backups'),
                    ])
                    ->compact(),
                ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('type')->badge(), 
                Tables\Columns\TextColumn::make('hostname'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->default('offline')
                    ->color(fn (string $state): string => match ($state) {
                        'online' => 'success',
                        'offline' => 'danger',
                        default => 'warning', 
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('test_connection')
                    ->label('Test Connection')
                    ->icon('heroicon-m-signal')
                    ->action(function (RemoteBackupServer $record) {
                        $record->healthCheck();
                        
                        if ($record->status === 'online') {
                            Notification::make()
                                ->title('Connection successful')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Connection failed')
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRemoteBackupServers::route('/'),
            'create' => Pages\CreateRemoteBackupServer::route('/create'),
            'edit' => Pages\EditRemoteBackupServer::route('/{record}/edit'),
        ];
    }
}
