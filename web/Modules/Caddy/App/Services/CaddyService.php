<?php

namespace Modules\Caddy\App\Services;

use App\Models\Domain;
use Exception;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class CaddyService
{
    protected string $binaryPath;
    protected string $configPath;
    protected string $pidFile;
    protected string $logPath;

    public function __construct()
    {
        $this->binaryPath = config('caddy.binary_path', '/usr/bin/caddy');
        $this->configPath = config('caddy.config_path', '/etc/caddy/Caddyfile');
        $this->pidFile = config('caddy.pid_file', '/var/run/caddy.pid');
        $this->logPath = config('caddy.log_path', '/var/log/caddy');
    }

    /**
     * Check if Caddy service is running
     */
    public function isRunning(): bool
    {
        try {
            $process = new Process(['systemctl', 'is-active', 'caddy']);
            $process->run();

            return $process->getOutput() === "active\n";
        } catch (Exception $e) {
            Log::error('Failed to check Caddy service status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Start Caddy service
     */
    public function start(): array
    {
        try {
            $process = new Process(['systemctl', 'start', 'caddy']);
            $process->run();

            if ($process->isSuccessful()) {
                Log::info('Caddy service started successfully');
                return ['success' => true, 'message' => 'Caddy service started successfully'];
            }

            $error = $process->getErrorOutput();
            Log::error('Failed to start Caddy service: ' . $error);
            return ['success' => false, 'message' => 'Failed to start Caddy: ' . $error];
        } catch (Exception $e) {
            Log::error('Exception starting Caddy service: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }

    /**
     * Stop Caddy service
     */
    public function stop(): array
    {
        try {
            $process = new Process(['systemctl', 'stop', 'caddy']);
            $process->run();

            if ($process->isSuccessful()) {
                Log::info('Caddy service stopped successfully');
                return ['success' => true, 'message' => 'Caddy service stopped successfully'];
            }

            $error = $process->getErrorOutput();
            Log::error('Failed to stop Caddy service: ' . $error);
            return ['success' => false, 'message' => 'Failed to stop Caddy: ' . $error];
        } catch (Exception $e) {
            Log::error('Exception stopping Caddy service: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }

    /**
     * Restart Caddy service
     */
    public function restart(): array
    {
        try {
            $process = new Process(['systemctl', 'restart', 'caddy']);
            $process->run();

            if ($process->isSuccessful()) {
                Log::info('Caddy service restarted successfully');
                return ['success' => true, 'message' => 'Caddy service restarted successfully'];
            }

            $error = $process->getErrorOutput();
            Log::error('Failed to restart Caddy service: ' . $error);
            return ['success' => false, 'message' => 'Failed to restart Caddy: ' . $error];
        } catch (Exception $e) {
            Log::error('Exception restarting Caddy service: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }

    /**
     * Reload Caddy configuration without restarting
     */
    public function reload(): array
    {
        try {
            $process = new Process(['systemctl', 'reload', 'caddy']);
            $process->run();

            if ($process->isSuccessful()) {
                Log::info('Caddy configuration reloaded successfully');
                return ['success' => true, 'message' => 'Caddy configuration reloaded successfully'];
            }

            $error = $process->getErrorOutput();
            Log::error('Failed to reload Caddy configuration: ' . $error);
            return ['success' => false, 'message' => 'Failed to reload Caddy: ' . $error];
        } catch (Exception $e) {
            Log::error('Exception reloading Caddy configuration: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }

    /**
     * Get Caddy service status information
     */
    public function getStatus(): array
    {
        $status = [
            'running' => false,
            'pid' => null,
            'uptime' => null,
            'memory' => null,
            'version' => null,
        ];

        try {
            // Check if running
            $status['running'] = $this->isRunning();

            if ($status['running']) {
                // Get PID
                $pidProcess = new Process(['systemctl', 'show', '--property=MainPID', 'caddy']);
                $pidProcess->run();
                if ($pidProcess->isSuccessful()) {
                    $pidOutput = $pidProcess->getOutput();
                    if (preg_match('/MainPID=(\d+)/', $pidOutput, $matches)) {
                        $status['pid'] = $matches[1];
                    }
                }

                // Get uptime and memory info
                if ($status['pid']) {
                    $psProcess = new Process(['ps', '-p', $status['pid'], '-o', 'etime,rss', '--no-headers']);
                    $psProcess->run();
                    if ($psProcess->isSuccessful()) {
                        $psOutput = trim($psProcess->getOutput());
                        $parts = preg_split('/\s+/', $psOutput);
                        if (count($parts) >= 2) {
                            $status['uptime'] = $parts[0];
                            $status['memory'] = round($parts[1] / 1024, 2) . ' MB';
                        }
                    }
                }
            }

            // Get version
            $versionProcess = new Process([$this->binaryPath, 'version']);
            $versionProcess->run();
            if ($versionProcess->isSuccessful()) {
                $versionOutput = $versionProcess->getOutput();
                if (preg_match('/v([\d\.]+)/', $versionOutput, $matches)) {
                    $status['version'] = $matches[1];
                }
            }
        } catch (Exception $e) {
            Log::error('Error getting Caddy status: ' . $e->getMessage());
        }

        return $status;
    }

    /**
     * Validate Caddy configuration
     */
    public function validateConfig(): array
    {
        try {
            $process = new Process([$this->binaryPath, 'validate', '--config', $this->configPath]);
            $process->run();

            if ($process->isSuccessful()) {
                return ['valid' => true, 'message' => 'Configuration is valid'];
            }

            $error = $process->getErrorOutput();
            return ['valid' => false, 'message' => 'Configuration error: ' . $error];
        } catch (Exception $e) {
            Log::error('Exception validating Caddy configuration: ' . $e->getMessage());
            return ['valid' => false, 'message' => 'Exception: ' . $e->getMessage()];
        }
    }

    /**
     * Get Caddy version information
     */
    public function getVersion(): ?string
    {
        try {
            $process = new Process([$this->binaryPath, 'version']);
            $process->run();

            if ($process->isSuccessful()) {
                $output = $process->getOutput();
                if (preg_match('/v([\d\.]+)/', $output, $matches)) {
                    return $matches[1];
                }
            }
        } catch (Exception $e) {
            Log::error('Error getting Caddy version: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get configuration statistics
     */
    public function getConfigStats(): array
    {
        $stats = [
            'domains' => 0,
            'ssl_certs' => 0,
            'last_update' => null,
        ];

        try {
            // Count domains from database
            $stats['domains'] = Domain::count();

            // Get config file modification time
            if (file_exists($this->configPath)) {
                $stats['last_update'] = date('Y-m-d H:i:s', filemtime($this->configPath));
            }

            // Count SSL certificates
            $certDir = '/var/lib/caddy/.local/share/caddy/certificates';
            if (is_dir($certDir)) {
                $certs = glob($certDir . '/*/certificates/*.crt');
                $stats['ssl_certs'] = count($certs);
            }
        } catch (Exception $e) {
            Log::error('Error getting config stats: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Perform health checks
     */
    public function getHealthChecks(): array
    {
        $checks = [];

        // Service running check
        $checks[] = [
            'name' => 'Service Status',
            'description' => 'Caddy service is running',
            'status' => $this->isRunning() ? 'healthy' : 'unhealthy',
            'last_checked' => now()->format('H:i:s'),
        ];

        // Configuration validity check
        $configCheck = $this->validateConfig();
        $checks[] = [
            'name' => 'Configuration',
            'description' => 'Caddyfile syntax is valid',
            'status' => $configCheck['valid'] ? 'healthy' : 'unhealthy',
            'last_checked' => now()->format('H:i:s'),
        ];

        // Log directory writability
        $checks[] = [
            'name' => 'Log Directory',
            'description' => 'Log directory is writable',
            'status' => is_writable(dirname($this->logPath)) ? 'healthy' : 'unhealthy',
            'last_checked' => now()->format('H:i:s'),
        ];

        // Config file readability
        $checks[] = [
            'name' => 'Configuration File',
            'description' => 'Caddyfile is readable',
            'status' => is_readable($this->configPath) ? 'healthy' : 'unhealthy',
            'last_checked' => now()->format('H:i:s'),
        ];

        // Binary availability
        $checks[] = [
            'name' => 'Binary',
            'description' => 'Caddy binary is executable',
            'status' => is_executable($this->binaryPath) ? 'healthy' : 'unhealthy',
            'last_checked' => now()->format('H:i:s'),
        ];

        return $checks;
    }

    /**
     * Get recent activity/logs
     */
    public function getRecentActivity(): array
    {
        $activity = [];

        try {
            // Get systemd journal entries for Caddy
            $process = new Process(['journalctl', '-u', 'caddy', '-n', '10', '--no-pager', '--output=json']);
            $process->run();

            if ($process->isSuccessful()) {
                $lines = explode("\n", trim($process->getOutput()));
                foreach ($lines as $line) {
                    if (empty($line)) continue;

                    $entry = json_decode($line, true);
                    if ($entry) {
                        $type = 'info';
                        $message = $entry['MESSAGE'] ?? '';

                        // Determine type based on message content
                        if (strpos($message, 'error') !== false || strpos($message, 'failed') !== false) {
                            $type = 'error';
                        } elseif (strpos($message, 'warning') !== false || strpos($message, 'warn') !== false) {
                            $type = 'warning';
                        } elseif (strpos($message, 'started') !== false || strpos($message, 'reloaded') !== false) {
                            $type = 'success';
                        }

                        $activity[] = [
                            'type' => $type,
                            'message' => $message,
                            'timestamp' => $entry['__REALTIME_TIMESTAMP'] ?? time(),
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            Log::error('Error getting recent activity: ' . $e->getMessage());
        }

        return array_slice($activity, 0, 10); // Return last 10 entries
    }
}
