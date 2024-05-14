<?php

namespace Modules\Customer\App\Filament\Resources;

use App\Filament\Enums\ServerApplicationType;
use App\Models\Domain;
use App\SupportedApplicationTypes;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use JaOcero\RadioDeck\Forms\Components\RadioDeck;
use Modules\Customer\App\Filament\Resources\DomainResource\Pages;
use Modules\Customer\App\Filament\Resources\DomainResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DomainResource extends Resource
{
    protected static ?string $model = Domain::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-europe-africa';

    protected static ?int $navigationSort = 0;

    protected static ?string $navigationGroup = 'Hosting';

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

                Tables\Columns\TextColumn::make('hostingSubscription.hostingPlan.name')
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
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
            'index' => Pages\ListDomains::route('/'),
            'create' => Pages\CreateDomain::route('/create'),
            'edit' => Pages\EditDomain::route('/{record}/edit'),
        ];
    }
}
