<?php

namespace Modules\Caddy\App\Jobs;

use App\MasterDomain;
use App\Models\Domain;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CaddyBuild implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $fixPermissions = false;

    /**
     * Create a new job instance.
     */
    public function __construct($fixPermissions = false)
    {
        $this->fixPermissions = $fixPermissions;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->buildCaddyConfiguration();
        } catch (\Exception $e) {
            \Log::error('Caddy build job failed: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
            ]);

            // Attempt recovery
            $this->attemptRecovery();

            // Re-throw exception for job retry mechanism
            throw $e;
        }
    }

    /**
     * Build Caddy configuration
     */
    protected function buildCaddyConfiguration(): void
    {
        // Check if Caddy is enabled
        $caddyEnabled = setting('caddy.enabled') ?? false;
        if (!$caddyEnabled) {
            \Log::info('Caddy is disabled, skipping configuration build');
            return;
        }

        // Validate prerequisites
        $this->validatePrerequisites();

        // Auto-configure Apache if enabled
        if (setting('caddy.auto_configure_apache', true)) {
            $this->configureApacheForCaddy();
        }

        // Build configuration
        $this->generateCaddyfile();

        // Validate generated configuration
        $this->validateGeneratedConfig();

        // Apply configuration
        $this->applyCaddyConfiguration();
    }

    /**
     * Validate prerequisites before building configuration
     */
    protected function validatePrerequisites(): void
    {
        $caddyConfigPath = '/etc/caddy/Caddyfile';
        $caddyLogPath = '/var/log/caddy';

        // Check if Caddy config directory exists and is writable
        $configDir = dirname($caddyConfigPath);
        if (!is_dir($configDir)) {
            if (!mkdir($configDir, 0755, true)) {
                throw new \Exception("Cannot create Caddy config directory: {$configDir}");
            }
        }

        if (!is_writable($configDir)) {
            throw new \Exception("Caddy config directory is not writable: {$configDir}");
        }        // Check if log directory exists and is writable
        if (!is_dir($caddyLogPath)) {
            if (!mkdir($caddyLogPath, 0777, true)) {
                throw new \Exception("Cannot create Caddy log directory: {$caddyLogPath}");
            }
            // Ensure caddy user can write to the log directory with broader permissions
            shell_exec("chown -R caddy:caddy {$caddyLogPath}");
            shell_exec("chmod -R 777 {$caddyLogPath}");
        }

        if (!is_writable($caddyLogPath)) {
            // Try to fix permissions with broader access (777) for multi-user write access
            shell_exec("chown -R caddy:caddy {$caddyLogPath}");
            shell_exec("chmod -R 777 {$caddyLogPath}");

            if (!is_writable($caddyLogPath)) {
                throw new \Exception("Caddy log directory is not writable: {$caddyLogPath}");
            }
        }
    }

    /**
     * Configure Apache to work with Caddy
     */
    protected function configureApacheForCaddy(): void
    {
        try {
            // Get current Apache configuration
            $currentHttpPort = setting('general.apache_http_port', '80');
            $currentHttpsPort = setting('general.apache_https_port', '443');
            $targetHttpPort = setting('caddy.apache_proxy_port', '8080');
            $targetHttpsPort = setting('caddy.apache_proxy_https_port', '8443');

            // Update Apache ports if they conflict with Caddy
            if ($currentHttpPort == '80') {
                setting(['general.apache_http_port' => $targetHttpPort]);
                \Log::info("Updated Apache HTTP port from {$currentHttpPort} to {$targetHttpPort}");
            }

            if ($currentHttpsPort == '443') {
                setting(['general.apache_https_port' => $targetHttpsPort]);
                \Log::info("Updated Apache HTTPS port from {$currentHttpsPort} to {$targetHttpsPort}");
            }

            // Disable SSL in Apache since Caddy will handle it
            if (setting('general.apache_ssl_enabled', true)) {
                setting(['general.apache_ssl_enabled' => false]);
                \Log::info("Disabled SSL in Apache - Caddy will handle SSL termination");
            }

            // Update Caddy proxy port setting
            setting(['caddy.apache_proxy_port' => $targetHttpPort]);
            setting(['caddy.apache_proxy_https_port' => $targetHttpsPort]);

            \Log::info("Apache configured for Caddy integration");

        } catch (\Exception $e) {
            \Log::error("Failed to configure Apache for Caddy: " . $e->getMessage());
            throw new \Exception("Apache configuration failed: " . $e->getMessage());
        }
    }

    /**
     * Generate Caddyfile content
     */
    protected function generateCaddyfile(): void
    {
        $getAllDomains = Domain::whereNot('status', '<=>', 'broken')->get();
        $caddyBlocks = [];

        // Get Apache port settings (non-SSL ports for proxying)
        $apacheHttpPort = setting('caddy.apache_proxy_port') ?? setting('general.apache_http_port') ?? '8080';
        $caddyEmail = setting('caddy.email') ?? setting('general.master_email') ?? 'admin@localhost';

        // Get static file paths from settings
        $staticPaths = setting('caddy.static_paths') ?? '';

        foreach ($getAllDomains as $domain) {
            $isBroken = false;

            if ($domain->status === 'broken') {
                continue;
            }

            // Check if domain is valid
            if (!filter_var($domain->domain, FILTER_VALIDATE_DOMAIN)) {
                $isBroken = true;
            }

            if ($isBroken) {
                continue;
            }
            $domainLog = '/var/log/caddy/' . $domain->domain . '.log';
            shell_exec("chown caddy:caddy '/var/log/caddy/");
            shell_exec("chmod -R 777 /var/log/caddy/");


            shell_exec("sudo setfacl -R -m u:caddy:rx " . $domain->document_root);
            shell_exec("sudo setfacl -R -m u:caddy:rx " . $domain->domain_public);
            shell_exec("sudo setfacl -R -m u:caddy:rx " . $domain->home_root);



            // Set permissions for Caddy to access user directories
            shell_exec("chmod o+x {$domain->home_root}");
            shell_exec("chmod -R o+rX {$domain->document_root}");


            if (!file_exists($domainLog)) {
                // Create log file for the domain if it doesn't exist
                touch($domainLog);
                shell_exec("chown caddy:caddy {$domainLog}");
                shell_exec("chmod 777 {$domainLog}");
            }


            // Create Caddy block for SSL termination and proxy to Apache
            $caddyBlock = $this->createCaddyBlock($domain, $apacheHttpPort);
            if ($caddyBlock) {
                $caddyBlocks[] = $caddyBlock;
            }
        }

        // Add master domain if configured
        if (!empty(setting('general.master_domain'))) {
            $masterDomain = new MasterDomain();
            $masterDomainBlock = $this->createMasterDomainCaddyBlock($masterDomain, $apacheHttpPort);
            if ($masterDomainBlock) {
                $caddyBlocks[] = $masterDomainBlock;
            }
        }

        // Generate Caddyfile
        $caddyfile = view('caddy::caddyfile-build', [
            'caddyBlocks' => $caddyBlocks,
            'caddyEmail' => $caddyEmail,
            'staticPaths' => $staticPaths,
        ])->render();

        $caddyfile = preg_replace('~(*ANY)\A\s*\R|\s*(?!\r\n)\s$~mu', '', $caddyfile);

        // Write Caddyfile
        file_put_contents('/etc/caddy/Caddyfile-process', $caddyfile);
        shell_exec('cp /etc/caddy/Caddyfile-process /etc/caddy/Caddyfile');

        // Reload Caddy configuration
        shell_exec('systemctl reload caddy');
    }

    private function createCaddyBlock(Domain $domain, $apacheHttpPort): ?array
    {
        if ($domain->status === Domain::STATUS_SUSPENDED ||
            $domain->status === Domain::STATUS_DEACTIVATED ||
            $domain->status === Domain::STATUS_BROKEN) {
            return null;
        }

        return [
            'domain' => $domain->domain,
            'proxy_to' => "127.0.0.1:{$apacheHttpPort}",
            'enable_ssl' => true,
            'enable_www' => true,
            'document_root' => $domain->domain_public ?? "{$domain->home_root}/public_html",
        ];
    }

    private function createMasterDomainCaddyBlock(MasterDomain $masterDomain, $apacheHttpPort): ?array
    {
        if (empty($masterDomain->domain)) {
            return null;
        }

        return [
            'domain' => $masterDomain->domain,
            'proxy_to' => "127.0.0.1:{$apacheHttpPort}",
            'enable_ssl' => true,
            'enable_www' => true,
            'is_master' => true,
            'document_root' => $masterDomain->document_root ?? "/var/www/{$masterDomain->domain}/public_html",
        ];
    }

    /**
     * Validate generated configuration before applying
     */
    protected function validateGeneratedConfig(): void
    {
        $caddyConfigPath = '/etc/caddy/Caddyfile';
        $caddyBinary = '/usr/bin/caddy';

        if (!file_exists($caddyConfigPath)) {
            throw new \Exception("Generated Caddyfile not found at: {$caddyConfigPath}");
        }

        // Format Caddyfile to fix inconsistencies if Caddy binary is available
        if (is_executable($caddyBinary)) {
            $formatCommand = "{$caddyBinary} fmt --overwrite {$caddyConfigPath} 2>&1";
            $formatOutput = shell_exec($formatCommand);
            $formatExitCode = shell_exec("echo $?");

            if (trim($formatExitCode) === '0') {
                \Log::info('Caddyfile formatted successfully');
            } else {
                \Log::warning('Caddyfile formatting failed: ' . $formatOutput);
            }

            // Validate syntax using Caddy binary
            $command = "{$caddyBinary} validate --config {$caddyConfigPath} 2>&1";
            $output = shell_exec($command);
            $exitCode = shell_exec("echo $?");

            if (trim($exitCode) !== '0') {
                throw new \Exception("Caddyfile validation failed: {$output}");
            }

            \Log::info('Caddyfile validation passed');
        } else {
            \Log::warning('Caddy binary not found, skipping syntax validation and formatting');
        }
    }

    /**
     * Apply Caddy configuration and reload service
     */
    protected function applyCaddyConfiguration(): void
    {
        try {
            // Create backup of current configuration
            $this->backupCurrentConfig();

            // Reload Caddy service to apply new configuration
            $this->reloadCaddyService();

            \Log::info('Caddy configuration applied successfully');

            $this->cleanupOldBackups();
        } catch (\Exception $e) {
            \Log::error('Failed to apply Caddy configuration: ' . $e->getMessage());

            // Restore backup on failure
            $this->restoreConfigBackup();
            throw $e;
        }
    }

    /**
     * Create backup of current configuration
     */
    protected function backupCurrentConfig(): void
    {
        $caddyConfigPath = '/etc/caddy/Caddyfile';
        $backupPath = $caddyConfigPath . '.backup.' . date('Y-m-d-H-i-s');

        if (file_exists($caddyConfigPath)) {
            if (!copy($caddyConfigPath, $backupPath)) {
                throw new \Exception("Failed to create configuration backup at: {$backupPath}");
            }

            \Log::info("Configuration backup created: {$backupPath}");
        }
    }

    /**
     * Restore configuration from backup
     */
    protected function restoreConfigBackup(): void
    {
        $caddyConfigPath = '/etc/caddy/Caddyfile';
        $backupDir = dirname($caddyConfigPath);

        // Find the most recent backup
        $backups = glob($backupDir . '/Caddyfile.backup.*');
        if (!empty($backups)) {
            rsort($backups); // Sort by name (newest first)
            $latestBackup = $backups[0];

            if (copy($latestBackup, $caddyConfigPath)) {
                \Log::info("Configuration restored from backup: {$latestBackup}");
                $this->reloadCaddyService();
            } else {
                \Log::error("Failed to restore configuration from backup: {$latestBackup}");
            }
        }
    }

    /**
     * Reload Caddy service
     */
    protected function reloadCaddyService(): void
    {
        $commands = [
            // 'systemctl reload caddy',
            'systemctl restart caddy',

        ];

        foreach ($commands as $command) {
            $output = shell_exec("{$command} 2>&1");
            $exitCode = shell_exec("echo $?");

            if (trim($exitCode) === '0') {
                \Log::info("Caddy service reloaded successfully using: {$command}");
                return;
            }

            \Log::warning("Command failed: {$command}, output: {$output}");
        }

        throw new \Exception("Failed to reload Caddy service");
    }

    /**
     * Attempt recovery on job failure
     */
    protected function attemptRecovery(): void
    {
        try {
         //   \Log::info('Attempting Caddy configuration recovery');

            // Try to restore from backup
            $this->restoreConfigBackup();

            // Check if service is still running
            $status = shell_exec('systemctl is-active caddy 2>/dev/null');
            if (trim($status) !== 'active') {
            //    \Log::warning('Caddy service is not active, attempting to start');
                shell_exec('systemctl start caddy 2>&1');
            }

        //    \Log::info('Recovery attempt completed');
        } catch (\Exception $e) {
         //   \Log::error('Recovery attempt failed: ' . $e->getMessage());
        }
    }

    /**
     * Clean up old backup files
     */
    protected function cleanupOldBackups(): void
    {
        $caddyConfigPath = '/etc/caddy/Caddyfile';
        $backupDir = dirname($caddyConfigPath);
        $maxBackups = 10;

        $backups = glob($backupDir . '/Caddyfile.backup.*');
        if (count($backups) > $maxBackups) {
            rsort($backups); // Sort by name (newest first)
            $oldBackups = array_slice($backups, $maxBackups);

            foreach ($oldBackups as $backup) {
                if (unlink($backup)) {
                  //  \Log::info("Removed old backup: {$backup}");
                }
            }
        }
    }
}
