<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RemoteBackupServerResource\Pages;
use App\Filament\Resources\RemoteBackupServerResource\RelationManagers;
use App\Models\RemoteBackupServer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RemoteBackupServerResource extends Resource
{
    protected static ?string $model = RemoteBackupServer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Server Clustering';

    protected static ?string $navigationLabel = 'Backup Servers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required(),

                Forms\Components\Select::make('type')
                    ->label('Type')
                    ->options([
                        'ftp' => 'FTP',
                        'sftp' => 'SFTP',
                    ])
                    ->default('ftp')
                    ->required(),

                Forms\Components\TextInput::make('hostname')
                    ->label('Hostname')
                    ->required(),

                Forms\Components\TextInput::make('port')
                    ->label('Port')
                    ->default('21')
                    ->required(),

                Forms\Components\TextInput::make('username')
                    ->label('Username')
                    ->required(),

                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->required(),

                Forms\Components\TextInput::make('path')
                    ->label('Path')
                    ->default('/')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('hostname'),
                Tables\Columns\TextColumn::make('status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
