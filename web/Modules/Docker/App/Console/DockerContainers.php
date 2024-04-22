<?php

namespace Modules\Docker\App\Console;

use Illuminate\Console\Command;
use Modules\Docker\App\Models\DockerContainer;
use Modules\Docker\App\Models\DockerImage;
use Modules\Docker\DockerApi;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DockerContainers extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'docker:get-containers';

    /**
     * The console command description.
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dockerApi = new DockerApi();
        $containers = $dockerApi->getContainers();
        if (!empty($containers)) {
            foreach($containers as $container) {

                $findDockerContainer = DockerContainer::where('docker_id', $container['ID'])->first();

//                if ($findDockerContainer) {
//                    $findDockerContainer->delete();
//                }
//                continue;

                if (!$findDockerContainer) {
                    $findDockerContainer = new DockerContainer();
                    $findDockerContainer->docker_id = $container['ID'];
                    $findDockerContainer->environment_variables = [];
                }

                $findDockerContainer->name = $container['Image'];
                $findDockerContainer->image = $container['Image'];
                $findDockerContainer->command = $container['Command'];
                $findDockerContainer->labels = $container['Labels'];
                $findDockerContainer->local_volumes = $container['LocalVolumes'];
                $findDockerContainer->mounts = $container['Mounts'];
                $findDockerContainer->names = $container['Names'];
                $findDockerContainer->networks = $container['Networks'];
                $findDockerContainer->ports = $container['Ports'];
                $findDockerContainer->running_for = $container['RunningFor'];
                $findDockerContainer->size = $container['Size'];
                $findDockerContainer->state = $container['State'];
                $findDockerContainer->status = $container['Status'];
                $findDockerContainer->save();
            }
        }
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     */
    protected function getOptions(): array
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
