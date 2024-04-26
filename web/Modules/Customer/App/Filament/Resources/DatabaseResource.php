<?php

namespace Modules\Customer\App\Filament\Resources;

use App\Models\Database;
use Modules\Customer\App\Filament\Resources\DatabaseResource\Pages;
use Modules\Customer\App\Filament\Resources\DatabaseResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DatabaseResource extends Resource
{
    protected static ?string $model = Database::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('database_name')
                    ->prefix(function ($record) {
                        return $record->database_name_prefix;
                    })
                    ->label('Database Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('databaseUsers.username')
                    ->label('Database Users')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList(),

                Tables\Columns\TextColumn::make('is_remote_database_server')
                    ->badge()
                    ->state(fn($record) => $record->is_remote_database_server ? 'Remote Database Server' : 'Internal Database Server')
                    ->label('Database Server')
                    ->sortable(),

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
            'index' => Pages\ListDatabases::route('/'),
            'create' => Pages\CreateDatabase::route('/create'),
            'edit' => Pages\EditDatabase::route('/{record}/edit'),
        ];
    }
}
