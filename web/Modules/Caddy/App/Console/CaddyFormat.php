<?php

namespace Modules\Caddy\App\Console;

use Illuminate\Console\Command;

class CaddyFormat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caddy:format {--validate : Validate after formatting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Format Caddy configuration file to fix inconsistencies';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $caddyConfigPath = '/etc/caddy/Caddyfile';
        $caddyBinary = '/usr/bin/caddy';

        // Check if Caddyfile exists
        if (!file_exists($caddyConfigPath)) {
            $this->error('Caddyfile not found at: ' . $caddyConfigPath);
            return;
        }

        // Check if Caddy binary is available
        if (!is_executable($caddyBinary)) {
            $this->error('Caddy binary not found at: ' . $caddyBinary);
            return;
        }

        $this->info('Formatting Caddyfile...');

        // Create backup before formatting
        $backupPath = $caddyConfigPath . '.backup.format.' . date('Y-m-d-H-i-s');
        if (!copy($caddyConfigPath, $backupPath)) {
            $this->error('Failed to create backup. Aborting format operation.');
            return;
        }
        $this->info('Backup created: ' . $backupPath);

        // Format the Caddyfile
        $command = "{$caddyBinary} fmt --overwrite {$caddyConfigPath} 2>&1";
        $output = shell_exec($command);
        $exitCode = shell_exec("echo $?");

        if (trim($exitCode) === '0') {
            $this->info('✓ Caddyfile formatted successfully');
            
            // Validate if requested
            if ($this->option('validate')) {
                $this->validateCaddyfile();
            }
        } else {
            $this->error('✗ Failed to format Caddyfile: ' . $output);
            
            // Restore backup on failure
            if (copy($backupPath, $caddyConfigPath)) {
                $this->info('Backup restored due to formatting failure');
            }
        }
    }

    /**
     * Validate the Caddyfile
     */
    protected function validateCaddyfile(): void
    {
        $caddyConfigPath = '/etc/caddy/Caddyfile';
        $caddyBinary = '/usr/bin/caddy';

        $this->info('Validating Caddyfile...');
        
        $command = "{$caddyBinary} validate --config {$caddyConfigPath} 2>&1";
        $output = shell_exec($command);
        $exitCode = shell_exec("echo $?");

        if (trim($exitCode) === '0') {
            $this->info('✓ Caddyfile is valid');
        } else {
            $this->error('✗ Caddyfile validation failed:');
            $this->line($output);
        }
    }
}
