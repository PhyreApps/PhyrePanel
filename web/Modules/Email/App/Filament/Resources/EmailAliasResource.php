<?php

namespace Modules\Email\App\Filament\Resources;

use App\Models\Domain;
use Faker\Provider\Text;
use Modules\Email\App\Filament\Resources\EmailAliasResource\Pages;
use Modules\Email\App\Filament\Resources\EmailAliasResource\RelationManagers;
use Modules\Email\App\Models\EmailAlias;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmailAliasResource extends Resource
{
    protected static ?string $model = EmailAlias::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?string $navigationGroup = 'Email';

//    protected static ?string $label = 'Aliases';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\TextInput::make('address')
                    ->label('Forward')
                    ->helperText('To create a catch-all use an "*" as alias.')
                    ->required(),

                Forms\Components\Select::make('domain')
                    ->options(Domain::get()->pluck('domain', 'domain')->toArray()),

                Forms\Components\Textarea::make('goto')
                    ->label('To')
                    ->columnSpanFull()
                ->placeholder('Accepts multiple targets, one entry per line.')
                ->cols(20)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('forward'),

                Tables\Columns\TextColumn::make('goto')
                    ->label('To'),
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
            'index' => Pages\ListEmailAliases::route('/'),
            'create' => Pages\CreateEmailAlias::route('/create'),
            'edit' => Pages\EditEmailAlias::route('/{record}/edit'),
        ];
    }
}
