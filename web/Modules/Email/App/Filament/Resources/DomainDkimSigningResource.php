<?php

namespace Modules\Email\App\Filament\Resources;

use Faker\Provider\Text;
use Modules\Email\App\Filament\Resources\DomainDkimSigningResource\Pages;
use Modules\Email\App\Filament\Resources\DomainDkimSigningResource\RelationManagers;
use Modules\Email\App\Models\DomainDkimSigning;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DomainDkimSigningResource extends Resource
{
    protected static ?string $model = DomainDkimSigning::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Email';

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
                Tables\Columns\TextColumn::make('author'),
                Tables\Columns\TextColumn::make('dkim.domain_name'),
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
            'index' => Pages\ListDomainDkimSignings::route('/'),
            'create' => Pages\CreateDomainDkimSigning::route('/create'),
            'edit' => Pages\EditDomainDkimSigning::route('/{record}/edit'),
        ];
    }
}
