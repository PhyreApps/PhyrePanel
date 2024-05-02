<?php

namespace App\Filament\Resources;

use App\Filament\Enums\ServerApplicationType;
use App\Filament\Resources\HostingPlanResource\Pages;
use App\Models\HostingPlan;
use App\Models\RemoteDatabaseServer;
use App\SupportedApplicationTypes;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Tables;
use Filament\Tables\Table;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;

class HostingPlanResource extends Resource
{
    protected static ?string $model = HostingPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    protected static ?string $navigationGroup = 'Hosting Services';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        $remoteDatabaseServers = RemoteDatabaseServer::select(['name','id'])->get()->pluck('name', 'id');

        return $form
            ->schema([

                Forms\Components\Tabs::make('general')->schema([

                    Forms\Components\Tabs\Tab::make('General')->schema([

                        RadioDeck::make('default_server_application_type')
                            ->default('apache_php')
                            ->options(ServerApplicationType::class)
                            ->icons(ServerApplicationType::class)
                            ->descriptions(ServerApplicationType::class)
                            ->required()
                            ->live()
                            ->color('primary')
                            ->columns(2),

                        // PHP Configuration
                        Select::make('default_server_application_settings.php_version')
                            ->hidden(function (Get $get) {
                                return $get('default_server_application_type') !== 'apache_php';
                            })
                            ->default('8.3')
                            ->label('PHP Version')
                            ->options(SupportedApplicationTypes::getPHPVersions())
                            ->columns(5)
                            ->required(),

                        // End of PHP Configuration

                        // Node.js Configuration
                        Select::make('default_server_application_settings.nodejs_version')
                            ->hidden(function (Get $get) {
                                return $get('default_server_application_type') !== 'apache_nodejs';
                            })
                            ->label('Node.js Version')
                            ->default('20')
                            ->options(SupportedApplicationTypes::getNodeJsVersions())
                            ->columns(6)
                            ->required(),

                        // End of Node.js Configuration

                        // Python Configuration

                        Select::make('default_server_application_settings.python_version')
                            ->hidden(function (Get $get) {
                                return $get('default_server_application_type') !== 'apache_python';
                            })
                            ->label('Python Version')
                            ->default('3.10')
                            ->options(SupportedApplicationTypes::getPythonVersions())
                            ->columns(6)
                            ->required(),

                        // End of Python Configuration

                        // Ruby Configuration

                        Select::make('default_server_application_settings.ruby_version')
                            ->hidden(function (Get $get) {
                                return $get('server_application_type') !== 'apache_ruby';
                            })
                            ->label('Ruby Version')
                            ->default('3.4')
                            ->options(SupportedApplicationTypes::getRubyVersions())
                            ->columns(6)
                            ->required(),

                        RadioDeck::make('default_database_server_type')
                            ->live()
                            ->default('internal')
                            ->options([
                                'internal' => 'Internal',
                                'remote' => 'Remote',
                            ])
                            ->icons([
                                'internal' => 'phyre-database-marker',
                                'remote' => 'phyre-database-connect',
                            ])
                            ->descriptions([
                                'internal' => 'Use the internal database server.',
                                'remote' => 'Use a remote database server.',
                            ])
                            ->required()
                            ->color('primary')
                            ->columns(2),

                        Forms\Components\Select::make('default_remote_database_server_id')
                            ->label('Remote Database Server')
                            ->hidden(fn(Forms\Get $get): bool => 'remote' !== $get('default_database_server_type'))
                            ->options($remoteDatabaseServers),

                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->required(),

//                        Forms\Components\TextInput::make('slug')
//                            ->label('Slug')
//                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->label('Description'),


                    ]),

                    Forms\Components\Tabs\Tab::make('Hosting Parameters')->schema([
                        Forms\Components\TextInput::make('disk_space')
                            ->numeric()
                            ->default('1000')
                            ->suffix('GB')
                            ->label('Disk Space'),

                        Forms\Components\TextInput::make('bandwidth')
                            ->numeric()
                            ->default('10000')
                            ->suffix('GB')
                            ->label('Bandwidth'),

                        Forms\Components\TextInput::make('databases')
                            ->numeric()
                            ->default('5')
                            ->label('Databases'),

                        Forms\Components\TextInput::make('ftp_accounts')
                            ->numeric()
                            ->default('5')
                            ->label('FTP Accounts'),

                        Forms\Components\TextInput::make('email_accounts')
                            ->numeric()
                            ->default('5')
                            ->label('Email Accounts'),

                        Forms\Components\TextInput::make('subdomains')
                            ->numeric()
                            ->default('5')
                            ->label('Subdomains'),

                        Forms\Components\TextInput::make('parked_domains')
                            ->numeric()
                            ->default('5')
                            ->label('Parked Domains'),

                        Forms\Components\TextInput::make('addon_domains')
                            ->numeric()
                            ->default('5')
                            ->label('Addon Domains'),

                        Forms\Components\TextInput::make('ssl_certificates')
                            ->numeric()
                            ->default('1')
                            ->label('SSL Certificates'),

                        Forms\Components\TextInput::make('daily_backups')
                            ->numeric()
                            ->default('1')
                            ->label('Daily Backups'),

                        Forms\Components\TextInput::make('free_domain')
                            ->numeric()
                            ->default('1')
                            ->label('Free Domain'),
                    ]),

                     Forms\Components\Tabs\Tab::make('Advanced')->schema([

                         Forms\Components\Select::make('additional_services')
                             ->label('Additional Services')
                             ->options([
                                 'microweber' => 'Microweber',
                                 'wordpress' => 'WordPress',
                                 'opencart' => 'OpenCart',
                             ])
                             ->multiple(),

                         Forms\Components\Select::make('features')
                             ->label('Features')
                             ->options([
                                 'ssl' => 'SSL',
                                 'backup' => 'Backup',
                             ])
                             ->multiple(),
                     ])

                ])->columnSpanFull(),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Name'),

//                Tables\Columns\TextColumn::make('slug')
//                    ->searchable()
//                    ->label('Slug'),

                Tables\Columns\TextColumn::make('additional_services')
                    ->label('Additional Services'),

                Tables\Columns\TextColumn::make('features')
                    ->label('Features'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListHostingPlans::route('/'),
            'create' => Pages\CreateHostingPlan::route('/create'),
            'edit' => Pages\EditHostingPlan::route('/{record}/edit'),
        ];
    }
}
