<?php
namespace Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Entry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Modules\Docker\App\Models\DockerContainer;
use Modules\Docker\DockerApi;
use Modules\Docker\DockerContainerApi;
use Modules\Docker\Filament\Clusters\Docker\Resources\DockerContainerResource;

class ViewDockerContainer extends ViewRecord
{
    protected static string $resource = DockerContainerResource::class;

    public $containerLog = '';

    public function infolist(Infolist $infolist): Infolist
    {
        $dockerApi = new DockerApi();
//        $containerStats = $dockerApi->getContainerStats($this->record->docker_id);
//        $containerProcesses = $dockerApi->getContainerProcesses($this->record->docker_id);
        $containerLogs = $dockerApi->getContainerLogs($this->record->docker_id);
       // $container = $dockerApi->getContainerById($this->record->docker_id);

        if ($containerLogs) {
            $containerLogs = str_replace("\n", "<br>", $containerLogs);
            $this->containerLog = $containerLogs;
        }

        return $infolist
            ->schema([

//                ViewEntry::make('status')
//                    ->columnSpanFull()
//                    ->view('docker::filament.infolists.docker-container.actions')
//                    ->registerActions([
//                            Actions\Action::make('stop')
//                                ->label('Stop')
//                                ->color('primary'),
//
//                            Actions\Action::make('start')
//                                ->label('Start')
//                                ->color('primary'),
//
//                            Actions\Action::make('restart')
//                                ->label('Restart')
//                                ->color('success'),
//
//
//                            Actions\Action::make('recreate')
//                                ->label('Recreate')
//                                ->action('recreate')
//                                ->color('info'),
//
//
//                            Actions\Action::make('delete')
//                                ->label('Delete')
//                                ->color('danger'),
//                    ]),

                ViewEntry::make('containerLogs')
                    ->columnSpanFull()
                    ->view('docker::filament.infolists.docker-container.logs',[
                        'containerLog' => $containerLogs
                    ])

            ]);
    }

    public function recreate()
    {
        $dockerContainerApi = new DockerContainerApi();
        $dockerContainerApi->setImage($this->record->image);
        $dockerContainerApi->setEnvironmentVariables($this->record->environment_variables);
        $dockerContainerApi->setVolumeMapping($this->record->volume_mapping);
        $dockerContainerApi->setPort($this->record->port);
        $dockerContainerApi->setExternalPort($this->record->external_port);

        $newDockerImage = $dockerContainerApi->recreate($this->record->docker_id);
        if (!isset($newDockerImage['ID'])) {
            return false;
        }

        $this->record->image = $newDockerImage['Image'];
        $this->record->command = $newDockerImage['Command'];
        $this->record->labels = $newDockerImage['Labels'];
        $this->record->local_volumes = $newDockerImage['LocalVolumes'];
        $this->record->mounts = $newDockerImage['Mounts'];
        $this->record->names = $newDockerImage['Names'];
        $this->record->networks = $newDockerImage['Networks'];
        $this->record->ports = $newDockerImage['Ports'];
        $this->record->running_for = $newDockerImage['RunningFor'];
        $this->record->size = $newDockerImage['Size'];
        $this->record->save();

    }

    protected function getHeaderActions(): array
    {
        return [
          //  DeleteAction::make(),
        ];
    }
}
