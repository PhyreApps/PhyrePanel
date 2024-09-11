<?php

namespace Modules\Customer\App\Console;

use App\Models\GitRepository;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GitRepositoryMarkAsCloned extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'git-repository:mark-as-cloned {id}';

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
        $id = $this->argument('id');

        $repository = GitRepository::find($id);
        if (!$repository) {
            $this->error('Repository not found.');
            return;
        }

        $repository->status = GitRepository::STATUS_CLONED;
        $repository->save();
    }

    /**
     * Get the console command arguments.
     */
    protected function getArguments(): array
    {
        return [
            ['id', InputArgument::REQUIRED, 'Git repository ID.'],
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
