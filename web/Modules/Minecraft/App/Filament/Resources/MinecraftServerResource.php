<?php

namespace Modules\Minecraft\App\Filament\Resources;

use Modules\Minecraft\App\Filament\Resources\MinecraftServerResource\Pages;
use Modules\Minecraft\App\Filament\Resources\MinecraftServerResource\RelationManagers;
use Modules\Minecraft\App\Models\MinecraftServer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MinecraftServerResource extends Resource
{
    protected static ?string $model = MinecraftServer::class;

    protected static ?string $navigationIcon = 'minecraft-logo';

    protected static ?string $navigationGroup = 'Minecraft';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->placeholder('Enter the name of the server'),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip')
                    ->sortable(),
                Tables\Columns\TextColumn::make('port')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('players')
                    ->sortable(),
                Tables\Columns\TextColumn::make('world')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListMinecraftServers::route('/'),
            'create' => Pages\CreateMinecraftServer::route('/create'),
            'edit' => Pages\EditMinecraftServer::route('/{record}/edit'),
            'view' => Pages\ViewMinecraftServer::route('/{record}'),
        ];
    }
}
