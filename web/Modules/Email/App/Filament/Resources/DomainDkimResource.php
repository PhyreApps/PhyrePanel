<?php

namespace Modules\Email\App\Filament\Resources;

use Modules\Email\App\Filament\Resources\DomainDkimResource\Pages;
use Modules\Email\App\Filament\Resources\DomainDkimResource\RelationManagers;
use Modules\Email\App\Models\DomainDkim;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DomainDkimResource extends Resource
{
    protected static ?string $model = DomainDkim::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                Tables\Columns\TextColumn::make('domain_name'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('selector'),
//                Tables\Columns\TextColumn::make('private_key'),
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
            'index' => Pages\ListDomainDkims::route('/'),
            'create' => Pages\CreateDomainDkim::route('/create'),
            'edit' => Pages\EditDomainDkim::route('/{record}/edit'),
        ];
    }
}
