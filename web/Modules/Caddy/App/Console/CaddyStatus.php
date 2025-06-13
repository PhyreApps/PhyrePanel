<?php

namespace Modules\Caddy\App\Console;

use Illuminate\Console\Command;

class CaddyStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caddy:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Caddy service status';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Checking Caddy status...');

        // Check if Caddy is running
        $status = shell_exec('systemctl is-active caddy');
        $isRunning = trim($status) === 'active';

        // Get Caddy version
        $versionOutput = shell_exec('caddy version 2>/dev/null');
        $version = trim($versionOutput) ?: 'Unknown';

        // Check config validity
        $configCheck = shell_exec('caddy validate --config /etc/caddy/Caddyfile 2>&1');
        $configValid = strpos($configCheck, 'valid') !== false;

        // Display status
        $this->table(
            ['Property', 'Value'],
            [
                ['Service Status', $isRunning ? '<fg=green>Running</>' : '<fg=red>Stopped</>'],
                ['Version', $version],
                ['Configuration', $configValid ? '<fg=green>Valid</>' : '<fg=red>Invalid</>'],
                ['Enabled in Phyre', setting('caddy.enabled') ? '<fg=green>Yes</>' : '<fg=yellow>No</>'],
            ]
        );

        if (!$isRunning) {
            $this->warn('Caddy service is not running. You can start it with: systemctl start caddy');
        }

        if (!$configValid) {
            $this->error('Caddy configuration is invalid. Check the logs for details.');
        }
    }
}
