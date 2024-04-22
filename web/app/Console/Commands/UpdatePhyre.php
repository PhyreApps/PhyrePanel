<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdatePhyre extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Phyre to the latest version.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating Phyre...');

        $output = '';
        $output .= exec('mkdir -p /usr/local/phyre/update');
        $output .= exec('wget https://raw.githubusercontent.com/PhyreApps/PhyrePanel/main/update/update-web-panel.sh -O /usr/local/phyre/update/update-web-panel.sh');
        $output .= exec('chmod +x /usr/local/phyre/update/update-web-panel.sh');

        $this->info($output);

        shell_exec('bash /usr/local/phyre/update/update-web-panel.sh');

        $this->info('Phyre updated successfully.');
    }
}
