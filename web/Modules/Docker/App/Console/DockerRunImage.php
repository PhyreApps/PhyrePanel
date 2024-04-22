<?php

namespace Modules\Docker\App\Console;

use Illuminate\Console\Command;
use Modules\Docker\App\Models\DockerImage;
use Modules\Docker\DockerApi;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DockerRunImage extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'docker:run-image {name}';

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
          $name = $this->argument('name');
          $dockerImage = DockerImage::where('name', $name)->first();
          if ($dockerImage) {
                $this->info('Running image: ' . $dockerImage->name);

                $dockerApi = new DockerApi();
                $pullCommand = $dockerApi->runImage($name);

                $this->info($pullCommand);

          } else {
                $this->error('Image not found: ' . $name);
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
