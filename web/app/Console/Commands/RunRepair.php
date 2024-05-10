<?php

namespace app\Console\Commands;

use App\Models\Backup;
use App\Models\HostingSubscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RunRepair extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:run-repair';

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

        // Overwrite supervisor config file
        $workersCount = (int) setting('general.supervisor_workers_count');
        $supervisorConf = view('actions.samples.ubuntu.supervisor-conf', [
            'workersCount' => $workersCount
        ])->render();

        // Overwrite supervisor config file
        file_put_contents('/etc/supervisor/conf.d/phyre.conf', $supervisorConf);
        
        // Restart supervisor
        shell_exec('service supervisor restart');

        // Check supervisor config file
        $checkSupervisorStatus = shell_exec('service supervisor status');
        if (strpos($checkSupervisorStatus, 'active (running)') !== false) {
           $this->info('Supervisor is running');
        } else {
            $this->info('Supervisor is not running');
            $this->info('Restarting supervisor');
            shell_exec('service supervisor restart');
        }

        $checkApacheStatus = shell_exec('service apache2 status');
    }
}
