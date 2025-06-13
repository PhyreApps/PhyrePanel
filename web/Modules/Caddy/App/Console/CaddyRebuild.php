<?php

namespace Modules\Caddy\App\Console;

use Illuminate\Console\Command;
use Modules\Caddy\App\Jobs\CaddyBuild;

class CaddyRebuild extends Command
{    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caddy:rebuild {--format : Format Caddyfile after rebuilding}';

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

        // Format Caddyfile if requested or if formatting is needed
        if ($this->option('format') || $this->shouldFormatCaddyfile()) {
            $this->formatCaddyfile();
        }

        $this->info('Caddy configuration rebuilt successfully!');
    }

    /**
     * Check if Caddyfile needs formatting
     */
    protected function shouldFormatCaddyfile(): bool
    {
        $caddyConfigPath = '/etc/caddy/Caddyfile';
        $caddyBinary = '/usr/bin/caddy';

        if (!file_exists($caddyConfigPath) || !is_executable($caddyBinary)) {
            return false;
        }

        // Run caddy validate to check for formatting issues
        $output = shell_exec("{$caddyBinary} validate --config {$caddyConfigPath} 2>&1");
        
        // Check if output contains formatting warning
        return strpos($output, 'not formatted') !== false;
    }

    /**
     * Format the Caddyfile
     */
    protected function formatCaddyfile(): void
    {
        $caddyConfigPath = '/etc/caddy/Caddyfile';
        $caddyBinary = '/usr/bin/caddy';

        if (!file_exists($caddyConfigPath)) {
            $this->error('Caddyfile not found at: ' . $caddyConfigPath);
            return;
        }

        if (!is_executable($caddyBinary)) {
            $this->error('Caddy binary not found at: ' . $caddyBinary);
            return;
        }

        $this->info('Formatting Caddyfile...');
        
        $command = "{$caddyBinary} fmt --overwrite {$caddyConfigPath} 2>&1";
        $output = shell_exec($command);
        $exitCode = shell_exec("echo $?");

        if (trim($exitCode) === '0') {
            $this->info('Caddyfile formatted successfully');
        } else {
            $this->error('Failed to format Caddyfile: ' . $output);
        }
    }
}
