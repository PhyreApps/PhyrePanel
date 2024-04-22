<?php

namespace App\Filament\Resources;

use App\Filament\Enums\ServerApplicationType;
use App\Filament\Resources\DomainResource\Pages;
use App\Models\Domain;
use App\SupportedApplicationTypes;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;

class DomainResource extends Resource
{
    protected static ?string $model = Domain::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-europe-africa';

    protected static ?string $navigationGroup = 'Hosting Services';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        $hostingSubscriptions = [];
        $getHostingSubscriptions = \App\Models\HostingSubscription::all();
        foreach ($getHostingSubscriptions as $hostingSubscription) {
            $hostingSubscriptions[$hostingSubscription->id] = $hostingSubscription->domain . ' - '.$hostingSubscription->customer->name;
        }

        return $form
            ->schema([

                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('General')
                            ->schema([

                                Select::make('hosting_subscription_id')
                                    ->label('Hosting Subscription')
                                    ->options($hostingSubscriptions)
                                    ->columnSpanFull()
                                    ->required(),

                                TextInput::make('domain')
                                    ->unique(Domain::class, 'domain', ignoreRecord: true)
                                    ->label('Domain'),

                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        Domain::STATUS_ACTIVE => 'Active',
                                        Domain::STATUS_SUSPENDED => 'Suspended',
                                        Domain::STATUS_DEACTIVATED => 'Deactivated',
                                    ])
                                    ->default(Domain::STATUS_ACTIVE),

                                RadioDeck::make('server_application_type')
                                    ->default('apache_php')
                                    ->options(ServerApplicationType::class)
                                    ->icons(ServerApplicationType::class)
                                    ->descriptions(ServerApplicationType::class)
                                    ->required()
                                    ->live()
                                    ->color('primary')
                                    ->columns(3),

                                // PHP Configuration
                                Select::make('server_application_settings.php_version')
                                    ->hidden(function (Get $get) {
                                        return $get('server_application_type') !== 'apache_php';
                                    })
                                    ->default('8.3')
                                    ->label('PHP Version')
                                    ->options(SupportedApplicationTypes::getPHPVersions())
                                    ->columns(5)
                                    ->required(),

                                // End of PHP Configuration

                                // Node.js Configuration
                                Select::make('server_application_settings.nodejs_version')
                                    ->hidden(function (Get $get) {
                                        return $get('server_application_type') !== 'apache_nodejs';
                                    })
                                    ->label('Node.js Version')
                                    ->default('20')
                                    ->options(SupportedApplicationTypes::getNodeJsVersions())
                                    ->columns(6)
                                    ->required(),

                                // End of Node.js Configuration

                                // Python Configuration

                                Select::make('server_application_settings.python_version')
                                    ->hidden(function (Get $get) {
                                        return $get('server_application_type') !== 'apache_python';
                                    })
                                    ->label('Python Version')
                                    ->default('3.10')
                                    ->options(SupportedApplicationTypes::getPythonVersions())
                                    ->columns(6)
                                    ->required(),

                                // End of Python Configuration

                                // Ruby Configuration

                                Select::make('server_application_settings.ruby_version')
                                    ->hidden(function (Get $get) {
                                        return $get('server_application_type') !== 'apache_ruby';
                                    })
                                    ->label('Ruby Version')
                                    ->default('3.4')
                                    ->options(SupportedApplicationTypes::getRubyVersions())
                                    ->columns(6)
                                    ->required(),
                            ]),
                        Tabs\Tab::make('Git')
                            ->schema([

                                TextInput::make('git_repository_url')
                                    ->label('Repository URL'),

                                Actions::make([

                                    Actions\Action::make('clone_repository')
                                        //  ->icon('heroicon-m-refresh')
                                        //->requiresConfirmation()
                                        ->action(function(Get $get, $record) {

                                            // Run command
                                            $domainPublic = $record->domain_public;
                                            $gitRepositoryUrl = $get('git_repository_url');

                                            shell_exec('rm -rf ' . $domainPublic);
                                            $command = 'git clone '.$gitRepositoryUrl . ' ' . $domainPublic;
                                            $output = shell_exec($command);

                                            $record->configureVirtualHost();

                                        }),

                                ]),


                            ]),

                        Tabs\Tab::make('Node.js')
                            ->schema([

                                Tabs::make('Tabs Node.js')
                                    ->tabs([

                                Tabs\Tab::make('Dashboard')
                                    ->schema([

                                Actions::make([

                                    Actions\Action::make('restart_nodejs')
                                      //  ->icon('heroicon-m-refresh')
                                        ->requiresConfirmation()
                                        ->action(function() {
                                            // Restart Node.js
                                        }),

                               ]),

                               Select::make('nodejs_version')
                                    ->label('Node.js version')
                                    ->options([
                                        '14.x' => '14.x',
                                        '16.x' => '16.x',
                                    ])
                                    ->columnSpanFull()
                                    ->default('14.x'),

                                Select::make('package_manager')
                                    ->label('Package manager')
                                    ->options([
                                        'npm' => 'npm',
                                        'yarn' => 'yarn',
                                    ])
                                    ->columnSpanFull()
                                    ->default('npm'),

                                TextInput::make('document_root')
                                    ->label('Document root')
                                    ->columnSpanFull()
                                    ->default('/public_html'),

                                Select::make('application_mode')
                                    ->label('Application mode')
                                    ->options([
                                        'development' => 'Development',
                                        'production' => 'Production',
                                    ])
                                    ->columnSpanFull()
                                    ->default('production'),

                                TextInput::make('application_startup_file')
                                    ->label('Application startup file')
                                    ->columnSpanFull()
                                    ->default('app.js'),

                                KeyValue::make('custom_environment_variables')
                                    ->label('Custom Environment variables')
                                    ->columnSpanFull()
                                    ->helperText('Add custom environment variables for your Node.js application. Separate key and value with an equal sign. Example: KEY=VALUE')

                                 ]),

                                        Tabs\Tab::make('Run Node.js commands')
                                            ->schema([

                                                Select::make('node_version')
                                                    ->label('Node.js version')
                                                    ->options([
                                                        '14.x' => '14.x',
                                                        '16.x' => '16.x',
                                                    ])
                                                    ->default('14.x'),

                                                Select::make('package_manager')
                                                    ->label('Package manager')
                                                    ->options([
                                                        'npm' => 'npm',
                                                        'yarn' => 'yarn',
                                                    ])
                                                    ->default('npm'),

                                                TextInput::make('command')
                                                    ->label('Command')
                                                    ->default('start'),

                                                Actions::make([

                                                    Actions\Action::make('run_command')
                                                        //  ->icon('heroicon-m-refresh')
                                                        ->requiresConfirmation()
                                                        ->action(function() {
                                                            // Run command
                                                        }),

                                                ]),


                                        ]),

                                ])

                            ]),
                    ])
                    ->columnSpanFull()
                    ->activeTab(1),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('domain')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('hostingSubscription.customer.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hostingSubscription.hostingPlan.name')
                    ->searchable()
                    ->sortable(),

                //                Tables\Columns\TextColumn::make('customer.name')
                //                    ->searchable()
                //                    ->sortable(),

            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([

                Tables\Actions\Action::make('visit')
                    ->label('Open website')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn ($record): string => 'http://'.$record->domain, true),

                Tables\Actions\EditAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //  Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListDomains::route('/'),
            'create' => Pages\CreateDomain::route('/create'),
            'edit' => Pages\EditDomain::route('/{record}/edit'),
//            'view' => Pages\ViewDomain::route('/{record}'),
        ];
    }
}
