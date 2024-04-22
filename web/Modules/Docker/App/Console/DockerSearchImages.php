<?php

namespace Modules\Docker\App\Console;

use Illuminate\Console\Command;
use Modules\Docker\App\Models\DockerImage;
use Modules\Docker\DockerApi;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DockerSearchImages extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'docker:search-images {name}';

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
        $this->info('Display this on the screen');
        $name = $this->argument('name');
        $this->info('You have entered: ' . $name);

        $dockerApi = new DockerApi();
        $dockerSearch = $dockerApi->searchImages($name);

        foreach($dockerSearch as $dockerImage) {

            if ($dockerImage['IsOfficial'] == 'true') {
                $dockerImage['IsOfficial'] = 1;
            } else {
                $dockerImage['IsOfficial'] = 0;
            }
            if ($dockerImage['IsAutomated'] == 'true') {
                $dockerImage['IsAutomated'] = 1;
            } else {
                $dockerImage['IsAutomated'] = 0;
            }

            $findDockerImage = DockerImage::where('name', $dockerImage['Name'])->first();
            if ($findDockerImage === null) {
                $findDockerImage = new DockerImage();
                $findDockerImage->name = $dockerImage['Name'];
            }

            $findDockerImage->description = $dockerImage['Description'];
            $findDockerImage->star_count = $dockerImage['StarCount'];
            $findDockerImage->is_official = $dockerImage['IsOfficial'];
            $findDockerImage->is_automated = $dockerImage['IsAutomated'];
            $findDockerImage->save();

            $this->info('Name: ' . $dockerImage['Name']);
            $this->info('Description: ' . $dockerImage['Description']);
            $this->info('Stars: ' . $dockerImage['StarCount']);
            $this->info('Official: ' . $dockerImage['IsOfficial']);
            $this->info('Automated: ' . $dockerImage['IsAutomated']);
            $this->info('_______________________________________');
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
