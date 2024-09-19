<?php

namespace Modules\Email\App\Filament\Resources;

use App\Models\Domain;
use Modules\Email\App\Filament\Resources\EmailAliasDomainResource\Pages;
use Modules\Email\App\Filament\Resources\EmailAliasDomainResource\RelationManagers;
use Modules\Email\App\Models\EmailAliasDomain;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmailAliasDomainResource extends Resource
{
    protected static ?string $model = EmailAliasDomain::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?string $navigationGroup = 'Email';

    protected static ?string $label = 'Alias Domains';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('alias_domain')
                    ->options(Domain::get()->pluck('domain', 'domain')->toArray())
                    ->helperText('The domain that mails come in for.')
                    ->columnSpanFull(),

                Forms\Components\Select::make('target_domain')
                    ->options(Domain::get()->pluck('domain', 'domain')->toArray())
                    ->helperText('The domain where mails should go to.')
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('alias_domain')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('target_domain')
                    ->searchable()
                    ->sortable(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
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
            'index' => Pages\ListEmailAliasDomains::route('/'),
//            'create' => Pages\CreateEmailAliasDomain::route('/create'),
//            'edit' => Pages\EditEmailAliasDomain::route('/{record}/edit'),
        ];
    }
}
