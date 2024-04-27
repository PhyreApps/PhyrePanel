<?php

namespace Modules\Docker\Filament\Clusters\Docker\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Modules\Docker\App\Models\DockerContainer;
use Modules\Docker\App\Models\DockerImage;
use Modules\Docker\DockerApi;
use Modules\Docker\Filament\Clusters\DockerCluster;

class DockerCatalog extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Docker';

    protected static ?string $navigationLabel = 'Catalog';

    protected static ?string $slug = 'docker/catalog';

  //  protected static ?string $cluster = DockerCluster::class;

    protected static ?string $navigationIcon = 'docker-catalog';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'docker::filament.pages.docker-catalog';

    public $keyword = '';
    public $filterOfficial = true;
    public $filterAutomated = true;
    public $filterStarred = true;

    public $pullLog = '';
    public $pullLogFile = '';
    public $pullLogPulling = false;

    public $pullImageName = '';

    public function updatedKeyword()
    {
        if (empty($this->keyword)) {
            return;
        }

        Artisan::call('docker:search-images "' . $this->keyword.'"');
    }

    public function getPullLog()
    {
        if (file_exists($this->pullLogFile)) {
            $logContent = file_get_contents($this->pullLogFile);
            $this->pullLog = str_replace("\n", "<br>", $logContent);
        }

        if (str_contains($this->pullLog, 'DONE!')) {
            $this->pullLogPulling = false;
            $this->dispatch('close-modal', id: 'pull-docker-image');

            return $this->redirect('/admin/docker/containers/create?dockerImage=' . $this->pullImageName);
        }
    }

    public function pullDockerImage($dockerImageName)
    {
        $dockerImage = DockerImage::where('name', $dockerImageName)->first();
        if ($dockerImage) {

            $this->pullImageName = $dockerImageName;

            $this->dispatch('open-modal', id: 'pull-docker-image');

            $dockerLogPath = storage_path('logs/docker/pull-'.Str::slug($dockerImageName).'.log');
            if (!is_dir(dirname($dockerLogPath))) {
                shell_exec('mkdir -p '.dirname($dockerLogPath));
            }
            $this->pullLogFile = $dockerLogPath;

            $dockerApi = new DockerApi();
            $dockerApi->setLogFile(storage_path('logs/docker/pull-'.Str::slug($dockerImageName).'.log'));
            $dockerApi->pullImage($dockerImage->name);

            $this->pullLogPulling = true;

            $this->getPullLog();
        }
    }

    public function removeDockerContainer($containerId)
    {
        $findDockerContainer = DockerContainer::where('id', $containerId)->first();
        if ($findDockerContainer) {
            $dockerApi = new DockerApi();
            $dockerApi->removeContainerById($findDockerContainer->docker_id);
            $findDockerContainer->delete();
        }
    }

    protected function getViewData(): array
    {
        $findImagesQuery = DockerImage::query();
        if ($this->keyword) {
            $findImagesQuery->where('name', 'like', '%' . $this->keyword . '%');
        }
        $findImages = $findImagesQuery->get();

        $findDockerContainers = DockerContainer::orderBy('id', 'desc')->get();

        return [
            'dockerImages' => $findImages->toArray(),
            'dockerContainers' => $findDockerContainers->toArray(),
        ];
    }
    protected function getFormSchema(): array
    {
        return [

            TextInput::make('keyword')
                ->live()
                ->placeholder('Search for docker images...')
                ->label('Search'),

            Grid::make('4')
                ->schema([

                    Checkbox::make('filterOfficial')
                        ->label('Official'),

                    Checkbox::make('filterAutomated')
                        ->label('Automated'),

                    Checkbox::make('filterStarred')
                        ->label('Starred'),

                ])

        ];
    }

}
