<?php

namespace App\Filament\Resources;

use App\Filament\Enums\BackupStatus;
use App\Filament\Enums\BackupType;
use App\Filament\Resources\BackupResource\Pages;
use app\Filament\Resources\BackupResource\Widgets\BackupStats;
use App\Models\Backup;
use App\Models\HostingSubscription;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
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

                Tables\Columns\TextColumn::make('backup_type')
                    ->state(function (Backup $backup) {
                        return ucfirst($backup->backup_type);
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->badge(),

                Tables\Columns\TextColumn::make('completed_at')
                    ->state(function (Backup $backup) {
                        return $backup->completed_at ? $backup->completed_at : 'N/A';
                    }),

                Tables\Columns\TextColumn::make('size')
                    ->state(function (Backup $backup) {
                        return $backup->size ? $backup->size : 'N/A';
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (Backup $backup) {
                        $url = Storage::disk('backups')
                            ->temporaryUrl($backup->filepath, Carbon::now()->addMinutes(5));
                        return redirect($url);
                    }),
                Tables\Actions\ViewAction::make(),
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
    public static function getWidgets(): array
    {
        return [
            BackupStats::class,
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
