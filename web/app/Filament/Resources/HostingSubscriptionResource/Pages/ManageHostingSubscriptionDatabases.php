<?php

namespace app\Filament\Resources\HostingSubscriptionResource\Pages;


use App\Filament\Resources\Blog\PostResource;
use App\Filament\Resources\HostingSubscriptionResource;
use App\Models\DatabaseUser;
use App\Models\RemoteDatabaseServer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ManageHostingSubscriptionDatabases extends ManageRelatedRecords
{
    protected static string $resource = HostingSubscriptionResource::class;

    protected static string $relationship = 'databases';

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    public function getTitle(): string | Htmlable
    {
        $recordTitle = $this->getRecordTitle();

        $recordTitle = $recordTitle instanceof Htmlable ? $recordTitle->toHtml() : $recordTitle;

        return "Manage {$recordTitle} Databases";
    }

    public function getBreadcrumb(): string
    {
        return 'Databases';
    }

    public static function getNavigationLabel(): string
    {
        return 'Databases';
    }

    public function form(Form $form): Form
    {

        $systemUsername = $this->record->system_username;

        return $form
            ->schema([

                Forms\Components\ToggleButtons::make('is_remote_database_server')
                    ->default(0)
                    ->disabled(function ($record) {
                        return $record;
                    })
                    ->live()
                    ->options([
                        0 => 'Internal',
                        1 => 'Remote Database Server',
                    ])->inline(),

                Forms\Components\Select::make('remote_database_server_id')
                    ->label('Remote Database Server')
                    ->hidden(fn(Forms\Get $get): bool => '1' !== $get('is_remote_database_server'))
                    ->options(
                        RemoteDatabaseServer::all()->pluck('name', 'id')
                    ),

                Forms\Components\TextInput::make('database_name')
                    ->prefix($systemUsername.'_')
                    ->disabled(function ($record) {
                        return $record;
                    })
                    ->label('Database Name')
                    ->required(),

                Forms\Components\Repeater::make('databaseUsers')
                    ->relationship('databaseUsers')
                    ->schema([
                        Forms\Components\TextInput::make('username')
                            ->disabled(function ($record) {
                                return $record;
                            })
                            ->prefix(function ($record) use($systemUsername) {
                               if ($record) {
                                  return $record->username_prefix;
                               }
                               return $systemUsername.'_';
                            })
                            ->required(),
                        Forms\Components\TextInput::make('password')
                            ->disabled(function ($record) {
                                return $record;
                            })
                            //->password()
                            ->required(),
                    ])
                    ->columns(2)

            ])
            ->columns(1);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(1)
            ->schema([
                TextEntry::make('database_name')->label('Database Name'),
            ]);
    }

    public function table(Table $table): Table
    {

        $systemUsername = $this->record->system_username;

        return $table
            ->recordTitleAttribute('database_name')
            ->columns([

                Tables\Columns\TextColumn::make('database_name')
                    ->prefix(function ($record) {
                        return $record->database_name_prefix;
                    })
                    ->label('Database Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('databaseUsers.username')
                    ->label('Database Users')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList(),

                Tables\Columns\TextColumn::make('is_remote_database_server')
                    ->badge()
                    ->state(fn($record) => $record->is_remote_database_server ? 'Remote Database Server' : 'Internal Database Server')
                    ->label('Database Server')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
//                Tables\Actions\Action::make('Create Database User')
//                    ->modal('create-database-user')
//                ->form([
//
//                    Forms\Components\Select::make('database_id')
//                        ->label('Database')
//                        ->default($this->record->databases->first()?->id)
//                        ->options(
//                            $this->record->databases->pluck('database_name', 'id')
//                        )
//                        ->required(),
//
//                    Forms\Components\TextInput::make('username')
//                        ->prefix(function () use($systemUsername) {
//                            return $systemUsername;
//                        })
//                        ->label('Username')
//                        ->required(),
//
//                    Forms\Components\TextInput::make('password')
//                        ->password()
//                        ->label('Password')
//                        ->required(),
//                ])
//                ->afterFormValidated(function ($data) {
//
//                    $newDatabaseUser = new DatabaseUser();
//                    $newDatabaseUser->username = $data['username'];
//                    $newDatabaseUser->password = $data['password'];
//                    $newDatabaseUser->database_id = $data['database_id'];
//                    $newDatabaseUser->save();
//
//                }),
            ])
            ->actions([
               // Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->groupedBulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
