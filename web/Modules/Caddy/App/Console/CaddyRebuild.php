<?php

namespace Modules\Caddy\App\Console;

use Illuminate\Console\Command;
use Modules\Caddy\App\Jobs\CaddyBuild;

class CaddyRebuild extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caddy:rebuild';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild Caddy configuration';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Rebuilding Caddy configuration...');

        $caddyBuild = new CaddyBuild(true);
        $caddyBuild->handle();

        $this->info('Caddy configuration rebuilt successfully!');
    }
}
