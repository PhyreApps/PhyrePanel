<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApiKeyResource\Pages;
use App\Filament\Resources\ApiKeyResource\RelationManagers;
use App\Models\ApiKey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApiKeyResource extends Resource
{
    protected static ?string $model = ApiKey::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Server Management';

    protected static ?int $navigationSort = 5;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->autofocus()
                    ->required()
                    ->label('Name')->columnSpanFull(),
                Forms\Components\TextInput::make('api_key')
                    ->disabled()
                    ->columnSpanFull()
                    ->placeholder('Will be automatically generated when you create')
                    ->label('API Key'),
                Forms\Components\TextInput::make('api_secret')
                    ->disabled()
                    ->columnSpanFull()
                    ->placeholder('Will be automatically generated when you create')
                    ->label('API Secret'),

                Forms\Components\Toggle::make('enable_whitelisted_ips')
                    ->live()
                    ->columnSpanFull()
                    ->label('Enable Whitelisted IPs'),

                Forms\Components\TagsInput::make('whitelisted_ips')
                    ->hidden(fn(Forms\Get $get): bool => !$get('enable_whitelisted_ips'))
                    ->label('Whitelisted IPs')
                    ->placeholder('Add new ip address')
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
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
            'index' => Pages\ListApiKeys::route('/'),
            'create' => Pages\CreateApiKey::route('/create'),
            'edit' => Pages\EditApiKey::route('/{record}/edit'),
        ];
    }
}
