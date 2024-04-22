<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RemoteDatabaseServerResource\Pages;
use App\Filament\Resources\RemoteDatabaseServerResource\RelationManagers;
use App\Models\RemoteDatabaseServer;
use Faker\Provider\Text;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;

class RemoteDatabaseServerResource extends Resource
{
    protected static ?string $model = RemoteDatabaseServer::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationGroup = 'Server Clustering';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Database Servers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                RadioDeck::make('database_type')
                    ->default('mysql')
                    ->label('Database Type')
                    ->columnSpanFull()
                    ->options([
                        'mysql' => 'MySQL',
                        'mongodb' => 'MongoDB',
                        'sqlite' => 'SQLite',
                        'mariadb' => 'MariaDB',
                        'postgresql' => 'PostgreSQL',
                    ])
                    ->icons([
                        'mysql' => 'phyre-mysql',
                        'mariadb' => 'phyre-mariadb',
                        'mongodb' => 'phyre-mongodb',
                        'postgresql' => 'phyre-postgresql',
                        'sqlite' => 'phyre-sqlite',
                    ])
                    ->descriptions([
                        'mysql' => 'MySQL is an open-source relational database management system.',
                        'mariadb' => 'MariaDB is a community-developed fork of MySQL.',
                        'mongodb' => 'MongoDB is a cross-platform document-oriented database program.',
                        'postgresql' => 'PostgreSQL is a powerful object-relational database system.',
                        'sqlite' => 'SQLite is small, fast, self-contained, high-reliability, full-featured engine.',
                    ])
                    ->required()
                    ->color('primary')
                    ->columns(3),

                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->columnSpanFull()
                    ->placeholder('Enter a name for this server'),

                Forms\Components\TextInput::make('host')
                    ->label('Host / IP Address')
                    ->required()
                    ->placeholder('Enter the host or ip address of the server'),

                Forms\Components\TextInput::make('port')
                    ->label('Port')
                    ->required()
                    ->placeholder('Enter the port of the server'),

                Forms\Components\TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->columnSpanFull()
                    ->placeholder('Enter the username of the server'),

                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->required()
                    ->columnSpanFull()
                    ->placeholder('Enter the password of the server'),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('database_type')
                    ->label('Database Type')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('host')
                    ->label('Host / IP Address')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'offline' => 'danger',
                        'online' => 'success',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('refresh-status')
                    ->action(function($record) {
                        $record->healthCheck();
                })->label('Refresh Status'),
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
            'index' => Pages\ListRemoteDatabaseServers::route('/'),
            'create' => Pages\CreateRemoteDatabaseServer::route('/create'),
            'edit' => Pages\EditRemoteDatabaseServer::route('/{record}/edit'),
        ];
    }
}
