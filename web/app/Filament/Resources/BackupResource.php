<?php

namespace App\Filament\Resources;

use App\Filament\Enums\BackupType;
use App\Filament\Resources\BackupResource\Pages;
use App\Models\Backup;
use App\Models\HostingSubscription;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class BackupResource extends Resource
{
    protected static ?string $model = Backup::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $navigationGroup = 'Server Management';

    protected static ?int $navigationSort = 97;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                RadioDeck::make('backup_type')
                    ->live()
                    ->default('full_backup')
                    ->options(BackupType::class)
                    ->icons(BackupType::class)
                    ->descriptions(BackupType::class)
                    ->required()
                    ->color('primary')
                    ->columnSpanFull(),


                Select::make('hosting_subscription_id')
                    ->label('Hosting Subscription')
                    ->hidden(function (Get $get) {
                        return $get('backup_type') !== 'hosting_subscription';
                    })
                    ->options(
                        HostingSubscription::all()->pluck('domain', 'id')
                    )
                    ->columnSpanFull()
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('backup_type'),
                Tables\Columns\TextColumn::make('backupRelated'),
                Tables\Columns\TextColumn::make('status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('id', 'desc')
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
            'index' => Pages\ListBackups::route('/'),
            'create' => Pages\CreateBackup::route('/create'),
            'view' => Pages\ViewBackup::route('/{record}'),
        ];
    }
}
