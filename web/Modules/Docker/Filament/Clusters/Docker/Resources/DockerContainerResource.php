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
use Modules\Docker\DockerApi;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource\Pages\CreateDockerContainer;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource\Pages\EditDockerContainer;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource\Pages\ListDockerContainers;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource\Pages\ViewDockerContainer;
use Modules\Docker\Filament\Clusters\DockerCluster;


class DockerContainerResource extends Resource
{
    protected static ?string $model = DockerContainer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = DockerCluster::class;

    protected static ?int $navigationSort = 2;

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
        if ($defaultPort == 80) {
            $defaultPort = 83;
        }

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->default($dockerFullImageName)
                    ->label('Container Name')->columnSpanFull(),

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
                   // ->disabled()
                    ->default($defaultPort)
                    ->columnSpan(1),

                Forms\Components\TextInput::make('external_port')
                    ->label('External Port')
                  //  ->disabled()
                    ->default($defaultExternalPort)
                    ->columnSpan(1),


                Forms\Components\Select::make('image')
                    ->label('Image')
                    ->options([$dockerImage=>$dockerFullImageName])
                    ->default($dockerImage)->columnSpanFull(),

                Forms\Components\KeyValue::make('volume_mapping')
                    ->label('Volume Mapping')
                    ->columnSpanFull(),

                Forms\Components\KeyValue::make('environment_variables')
                    ->label('Environment Variables')
                    ->default($environmentVariables)
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

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
