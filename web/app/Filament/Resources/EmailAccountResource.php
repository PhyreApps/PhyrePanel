<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailAccountResource\Pages;
use App\Filament\Resources\EmailAccountResource\RelationManagers;
use App\Models\Domain;
use App\Models\EmailAccount;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmailAccountResource extends Resource
{
    protected static ?string $model = EmailAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('domain_id')
                    ->label('Domain')
                    ->helperText('Missing a domain? Check the Missing a domain? section to find out how you can create one.')
                    ->options(Domain::all()->pluck('domain', 'id'))
                    ->searchable(),
                Forms\Components\TextInput::make('username')
                    ->hint(str('[Missing a domain?](/missing-domain)')->inlineMarkdown()->toHtmlString()),
                Forms\Components\TextInput::make('password')
                    ->hint(str('[Forgotten your password?](/forgotten-password)')->inlineMarkdown()->toHtmlString())
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->maxLength(20),
                DatePicker::make('last_login')
                    ->format('d/m/Y'),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('domain'),
                TextColumn::make('username'),
                TextColumn::make('password'),
                TextColumn::make('created_ad'),
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
            'index' => Pages\ListEmailAccounts::route('/'),
            'create' => Pages\CreateEmailAccount::route('/create'),
            'edit' => Pages\EditEmailAccount::route('/{record}/edit'),
        ];
    }
}
