<?php

namespace App\Console\Commands;

use App\Models\Backup;
use App\Models\HostingSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyFullBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:create-daily-full-backup';

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
        $backup = new Backup();
        $backup->backup_type = 'full';
        $backup->save();
    }
}
