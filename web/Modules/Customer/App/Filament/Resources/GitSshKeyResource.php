<?php

namespace Modules\Customer\App\Filament\Resources;

use App\Models\Domain;
use App\Models\GitSshKey;
use Modules\Customer\App\Filament\Resources\GitSshKeyResource\Pages;
use Modules\Customer\App\Filament\Resources\GitSshKeyResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GitSshKeyResource extends Resource
{
    protected static ?string $model = GitSshKey::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Git';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('domain_id')
                    ->label('Domain')
                    ->options([])
                    ->columnSpanFull()
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListGitSshKeys::route('/'),
            'create' => Pages\CreateGitSshKey::route('/create'),
            'edit' => Pages\EditGitSshKey::route('/{record}/edit'),
        ];
    }
}
