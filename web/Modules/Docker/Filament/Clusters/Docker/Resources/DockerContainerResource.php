<?php

namespace Modules\Docker\Filament\Clusters\Docker\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Modules\Docker\App\Models\DockerContainer;
use Modules\Docker\App\Models\DockerTemplate;
use Modules\Docker\DockerApi;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource\Pages\CreateDockerContainer;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource\Pages\EditDockerContainer;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource\Pages\ListDockerContainers;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource\Pages\ViewDockerContainer;
use Modules\Docker\Filament\Clusters\DockerCluster;
use Riodwanto\FilamentAceEditor\AceEditor;


class DockerContainerResource extends Resource
{
    protected static ?string $navigationGroup = 'Docker';

    protected static ?string $navigationLabel = 'Containers';

    protected static ?string $slug = 'docker/containers';

    protected static ?string $model = DockerContainer::class;

    protected static ?string $navigationIcon = 'docker-logo';

//    protected static ?string $cluster = DockerCluster::class;

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $dockerImage = request('dockerImage', null);
        $environmentVariables = [];

        $dockerFullImageName = '';
        $dockerApi = new DockerApi();
        $dockerImageInspect = $dockerApi->getDockerImageInspect($dockerImage);
        if (isset($dockerImageInspect['RepoTags'][0])) {
            $dockerFullImageName = $dockerImageInspect['RepoTags'][0];
        }
        if (isset($dockerImageInspect['Config']['Env'])) {
            foreach ($dockerImageInspect['Config']['Env'] as $env) {
                $envParts = explode('=', $env);
                $environmentVariables[$envParts[0]] = $envParts[1];
            }
        }
        $defaultPort = '';
        $defaultExternalPort = 8010;

        // Get available ports from linux server
        $getAvailablePortShellFile = module_path('Docker', 'shell-scripts/get-available-port.sh');
        $availablePort = shell_exec("sh $getAvailablePortShellFile");
        if (is_numeric($availablePort)) {
            $defaultExternalPort = $availablePort;
        }

        if (isset($dockerImageInspect['Config']['ExposedPorts'])) {
            foreach ($dockerImageInspect['Config']['ExposedPorts'] as $port => $value) {
                $port = str_replace('/tcp', '', $port);
                $port = str_replace('/udp', '', $port);
                $port = str_replace('tcp', '', $port);
                $port = str_replace('udp', '', $port);
                $port = str_replace('/', '', $port);
                $defaultPort = $port;
            }
        }

        $dockerTemplateContent = '';
        $dockerTemplateId = request()->get('docker_template_id', null);
        $findDockerTemplate = DockerTemplate::find($dockerTemplateId);
        if ($findDockerTemplate) {
            $dockerTemplateContent = $findDockerTemplate->docker_compose;
        }
        $buildType = request()->get('build_type', null);

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->default($dockerFullImageName)
                    ->label('Container Name')->columnSpanFull(),

                Forms\Components\Select::make('build_type')
                    ->label('Build container from')
                    ->live()
                    ->default('image')
                    ->options([
                      //  'template'=>'Docker Template',
                        'image'=>'Docker Image'
                    ])
                    ->columnSpanFull(),

                Forms\Components\Select::make('docker_template_id')
                    ->label('Template')
                    ->default($dockerTemplateId)
                    ->hidden(function (Forms\Get $get) {
                        return $get('build_type') != 'template';
                    })
                    ->live()
                    ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?string $old, ?string $state) {
                        return redirect('/admin/docker/containers/create?build_type=template&docker_template_id=' . $state);
                    })
                    ->options(DockerTemplate::all()->pluck('name', 'id'))
                    ->columnSpanFull(),

                Forms\Components\Select::make('image')
                    ->label('Image')
                    ->hidden(function (Forms\Get $get) {
                        return $get('build_type') != 'image';
                    })
                    ->options([$dockerImage=>$dockerFullImageName])
                    ->default($dockerImage)->columnSpanFull(),

                AceEditor::make('docker_compose')
                    ->hidden(function (Forms\Get $get) {
                        return $get('build_type') != 'template';
                    })
                    ->mode('yml')
                    ->theme('github')
                    ->default($dockerTemplateContent)
                    ->columnSpanFull()
                    ->darkTheme('dracula'),

                Forms\Components\TextInput::make('memory_limit')
                    ->label('Memory Limit (MB)')
                    ->default(512)
                    ->numeric()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('unlimited_memory')
                    ->label('Unlimited Memory')
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('automatic_start')
                    ->label('Automatic start after system reboot')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('port')
                    ->label('Port')
                    ->hidden(function (Forms\Get $get) {
                        return $get('build_type') != 'image';
                    })
                   // ->disabled()
                    ->default($defaultPort)
                    ->columnSpan(1),

                Forms\Components\TextInput::make('external_port')
                    ->label('External Port')
                    ->hidden(function (Forms\Get $get) {
                        return $get('build_type') != 'image';
                    })
                  //  ->disabled()
                    ->default($defaultExternalPort)
                    ->columnSpan(1),

                Forms\Components\KeyValue::make('volume_mapping')
                    ->label('Volume Mapping')
                    ->hidden(function (Forms\Get $get) {
                        return $get('build_type') != 'image';
                    })
                    ->columnSpanFull(),

                Forms\Components\KeyValue::make('environment_variables')
                    ->label('Environment Variables')
                    ->hidden(function (Forms\Get $get) {
                        return $get('build_type') != 'image';
                    })
                    ->default($environmentVariables)
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('state')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'running' => 'success',
                        'exited' => 'danger',
                        'paused' => 'warning',
                        'restarting' => 'info',
                        'dead' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('image'),
                Tables\Columns\TextColumn::make('port'),
                Tables\Columns\TextColumn::make('external_port'),
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
            'index' => ListDockerContainers::route('/'),
            'create' => CreateDockerContainer::route('/create'),
            'edit' => EditDockerContainer::route('/{record}/edit'),
            'view'=> ViewDockerContainer::route('/{record}'),
        ];
    }
}
