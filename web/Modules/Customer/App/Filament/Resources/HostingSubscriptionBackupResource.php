<?php

namespace Modules\Customer\App\Filament\Resources;

use App\Filament\Enums\HostingSubscriptionBackupType;
use App\Models\HostingSubscriptionBackup;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;
use Modules\Customer\App\Filament\Resources\HostingSubscriptionBackupResource\Pages;
use Modules\Customer\App\Filament\Resources\HostingSubscriptionBackupResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HostingSubscriptionBackupResource extends Resource
{
    protected static ?string $model = HostingSubscriptionBackup::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    protected static ?string $label = 'Backup';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Hosting';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Select::make('hosting_subscription_id')
                    ->label('Hosting Subscription')
                    ->options(
                        \App\Models\HostingSubscription::all()->pluck('domain', 'id')
                    )
                    ->live()
                    ->disabled(function ($record) {
                        return $record;
                    })
                    ->columnSpanFull()
                    ->required(),

                RadioDeck::make('backup_type')
                    ->live()
                    //  ->default('full')
                    ->options(HostingSubscriptionBackupType::class)
                    ->icons(HostingSubscriptionBackupType::class)
                    ->descriptions(HostingSubscriptionBackupType::class)
                    ->required()
                    ->color('primary')
                    ->columnSpanFull(),

            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('backup_type')
                    ->state(function (HostingSubscriptionBackup $backup) {
                        return ucfirst($backup->backup_type);
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->badge(),

                Tables\Columns\TextColumn::make('completed_at')
                    ->state(function (HostingSubscriptionBackup $backup) {
                        return $backup->completed_at ? $backup->completed_at : 'N/A';
                    }),

                Tables\Columns\TextColumn::make('size')
                    ->state(function (HostingSubscriptionBackup $backup) {
                        return $backup->size ? $backup->size : 'N/A';
                    }),

                Tables\Columns\TextColumn::make('hostingSubscription.domain')
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
            'index' => Pages\ListHostingSubscriptionBackups::route('/'),
            'create' => Pages\CreateHostingSubscriptionBackup::route('/create'),
            'edit' => Pages\EditHostingSubscriptionBackup::route('/{record}/edit'),
        ];
    }
}
