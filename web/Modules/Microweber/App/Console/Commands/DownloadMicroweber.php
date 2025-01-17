<?php

namespace Modules\Microweber\App\Console\Commands;

use Illuminate\Console\Command;

class DownloadMicroweber extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'microweber:download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $download = new \Modules\Microweber\Jobs\DownloadMicroweber();
        $download->handle();
    }
}
