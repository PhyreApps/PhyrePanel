<?php

namespace App\Filament\Resources;

use App\BackupStorage;
use App\Filament\Enums\BackupStatus;
use App\Filament\Enums\BackupType;
use App\Filament\Resources\BackupResource\Pages;
use app\Filament\Resources\BackupResource\Widgets\BackupStats;
use App\Helpers;
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

    protected static bool $shouldRegisterNavigation = false;

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

                Tables\Columns\TextColumn::make('created_at')
                    ->state(function (Backup $backup) {
                        return $backup->created_at ? $backup->created_at : 'N/A';
                    }),

//                Tables\Columns\TextColumn::make('completed_at')
//                    ->label('Completed time')
//                    ->state(function (Backup $backup) {
//                        $diff = \Carbon\Carbon::parse($backup->completed_at)
//                            ->diffForHumans($backup->created_at);
//                        return $backup->completed_at ? $diff : 'N/A';
//                    }),

                Tables\Columns\TextColumn::make('size')
                    ->state(function (Backup $backup) {
                        return ($backup->size > 0) ? Helpers::getHumanReadableSize($backup->size) : 'N/A';
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\Action::make('cancel')
                    ->icon('heroicon-o-x-mark')
                    ->hidden(function (Backup $backup) {
                        return $backup->status !== BackupStatus::Processing;
                    })
                    ->action(function (Backup $backup) {

                        try {
                            $processIds = shell_exec('ps aux | grep -i ' . $backup->file_name . ' | grep -v grep | awk \'{print $2}\'');
                            if (!empty($processIds)) {
                                $processIds = explode("\n", $processIds);
                                foreach ($processIds as $processId) {
                                    $processId = trim($processId);
                                    if (!empty($processId)) {
                                        shell_exec('kill -9 ' . $processId);
                                    }
                                }
                            }
                            shell_exec('kill -9 ' . $backup->process_id);
                        } catch (\Exception $e) {
                            // do nothing
                        }

                        $backup->update([
                            'status' => BackupStatus::Cancelled,
                        ]);
                    }),

                Tables\Actions\Action::make('download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->hidden(function (Backup $backup) {
                        return $backup->status !== BackupStatus::Completed;
                    })
                    ->action(function (Backup $backup) {

                        $backupStorage = BackupStorage::getInstance($backup->root_path);
                        $tempUrl = $backupStorage->temporaryUrl($backup->file_name, Carbon::now()->addMinutes(5));

                        return redirect($tempUrl);
                    }),

                Tables\Actions\Action::make('viewLog')
                    ->label('View Log')
                    ->icon('heroicon-o-document')
                    ->hidden(function (Backup $backup) {
                        $hide = true;
                        if ($backup->status === BackupStatus::Processing || $backup->status === BackupStatus::Failed) {
                            $hide = false;
                        }
                        return $hide;
                    })
                    ->modalContent(function (Backup $backup) {
                        return view('filament.modals.view-livewire-component', [
                            'component' => 'backup-log',
                            'componentProps' => [
                                'backupId' => $backup->id,
                            ],
                        ]);
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
           // BackupStats::class,
        ];
    }

    public static function getPages(): array
    {
        return [
//            'index' => Pages\ListBackups::route('/'),
//            'create' => Pages\CreateBackup::route('/create'),
//            'view' => Pages\ViewBackup::route('/{record}'),
            'index' => Pages\ManageBackups::route('/'),
        ];
    }
}
