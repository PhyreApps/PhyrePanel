<?php

namespace App\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;
use Exception;

class CaddyService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('caddy');
    }

    /**
     * Check if Caddy service is running
     */
    public function isRunning(): bool
    {
        try {
            $result = Process::run('systemctl is-active caddy');
            return $result->successful() && trim($result->output()) === 'active';
        } catch (Exception $e) {
            Log::error('Failed to check Caddy service status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get Caddy version
     */
    public function getVersion(): ?string
    {
        try {
            $result = Process::run('caddy version');
            if ($result->successful()) {
                // Extract version from output like "v2.7.6 h1:w0NymbG2m9PcvKWsrXO6EEkY9Ru4FHCWNzv8wbbJ7kc="
                preg_match('/v(\d+\.\d+\.\d+)/', $result->output(), $matches);
                return $matches[1] ?? null;
            }
        } catch (Exception $e) {
            Log::error('Failed to get Caddy version: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Start Caddy service
     */
    public function start(): bool
    {
        try {
            $result = Process::run('systemctl start caddy');
            return $result->successful();
        } catch (Exception $e) {
            Log::error('Failed to start Caddy service: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Stop Caddy service
     */
    public function stop(): bool
    {
        try {
            $result = Process::run('systemctl stop caddy');
            return $result->successful();
        } catch (Exception $e) {
            Log::error('Failed to stop Caddy service: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Restart Caddy service
     */
    public function restart(): bool
    {
        try {
            $result = Process::run('systemctl restart caddy');
            return $result->successful();
        } catch (Exception $e) {
            Log::error('Failed to restart Caddy service: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Reload Caddy configuration
     */
    public function reload(): bool
    {
        try {
            $result = Process::run('systemctl reload caddy');
            return $result->successful();
        } catch (Exception $e) {
            Log::error('Failed to reload Caddy service: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Validate Caddy configuration
     */
    public function validateConfig(): array
    {
        try {
            $configPath = $this->config['config_path'];
            $result = Process::run("caddy validate --config {$configPath}");
            
            return [
                'valid' => $result->successful(),
                'output' => $result->output(),
                'error' => $result->errorOutput()
            ];
        } catch (Exception $e) {
            Log::error('Failed to validate Caddy config: ' . $e->getMessage());
            return [
                'valid' => false,
                'output' => '',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get service status details
     */
    public function getStatus(): array
    {
        try {
            $result = Process::run('systemctl status caddy --no-pager');
            
            return [
                'running' => $this->isRunning(),
                'output' => $result->output(),
                'version' => $this->getVersion(),
                'config_valid' => $this->validateConfig()['valid'],
                'uptime' => $this->getUptime()
            ];
        } catch (Exception $e) {
            Log::error('Failed to get Caddy status: ' . $e->getMessage());
            return [
                'running' => false,
                'output' => '',
                'version' => null,
                'config_valid' => false,
                'uptime' => null
            ];
        }
    }

    /**
     * Get service uptime
     */
    public function getUptime(): ?string
    {
        try {
            $result = Process::run('systemctl show caddy --property=ActiveEnterTimestamp --value');
            if ($result->successful() && !empty(trim($result->output()))) {
                $timestamp = trim($result->output());
                $startTime = new \DateTime($timestamp);
                $now = new \DateTime();
                $interval = $now->diff($startTime);
                
                return $interval->format('%d days, %h hours, %i minutes');
            }
        } catch (Exception $e) {
            Log::error('Failed to get Caddy uptime: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Get system logs
     */
    public function getLogs(int $lines = 100): string
    {
        try {
            $result = Process::run("journalctl -u caddy -n {$lines} --no-pager");
            return $result->successful() ? $result->output() : '';
        } catch (Exception $e) {
            Log::error('Failed to get Caddy logs: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Get access logs
     */
    public function getAccessLogs(int $lines = 100): string
    {
        try {
            $logPath = $this->config['access_log_path'] ?? '/var/log/caddy/access.log';
            if (file_exists($logPath)) {
                $result = Process::run("tail -n {$lines} {$logPath}");
                return $result->successful() ? $result->output() : '';
            }
        } catch (Exception $e) {
            Log::error('Failed to get Caddy access logs: ' . $e->getMessage());
        }
        
        return '';
    }

    /**
     * Get error logs
     */
    public function getErrorLogs(int $lines = 100): string
    {
        try {
            $logPath = $this->config['error_log_path'] ?? '/var/log/caddy/error.log';
            if (file_exists($logPath)) {
                $result = Process::run("tail -n {$lines} {$logPath}");
                return $result->successful() ? $result->output() : '';
            }
        } catch (Exception $e) {
            Log::error('Failed to get Caddy error logs: ' . $e->getMessage());
        }
        
        return '';
    }

    /**
     * Check if port is available
     */
    public function isPortAvailable(int $port): bool
    {
        try {
            $result = Process::run("netstat -tuln | grep :{$port}");
            return !$result->successful() || empty(trim($result->output()));
        } catch (Exception $e) {
            Log::error("Failed to check port {$port} availability: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Test Apache connectivity
     */
    public function testApacheConnection(): bool
    {
        try {
            $apachePort = $this->config['apache_port'] ?? 8080;
            $result = Process::run("curl -s -o /dev/null -w '%{http_code}' http://localhost:{$apachePort}");
            
            if ($result->successful()) {
                $httpCode = trim($result->output());
                // Accept any response that's not a connection error
                return !empty($httpCode) && $httpCode !== '000';
            }
        } catch (Exception $e) {
            Log::error('Failed to test Apache connection: ' . $e->getMessage());
        }
        
        return false;
    }

    /**
     * Get disk usage for Caddy data directory
     */
    public function getDiskUsage(): array
    {
        try {
            $dataPath = $this->config['data_path'] ?? '/var/lib/caddy';
            $result = Process::run("du -sh {$dataPath}");
            
            if ($result->successful()) {
                $output = trim($result->output());
                $parts = explode("\t", $output);
                return [
                    'size' => $parts[0] ?? '0B',
                    'path' => $dataPath
                ];
            }
        } catch (Exception $e) {
            Log::error('Failed to get Caddy disk usage: ' . $e->getMessage());
        }
        
        return [
            'size' => '0B',
            'path' => $this->config['data_path'] ?? '/var/lib/caddy'
        ];
    }

    /**
     * Get SSL certificate info for a domain
     */
    public function getCertificateInfo(string $domain): ?array
    {
        try {
            $result = Process::run("echo | openssl s_client -servername {$domain} -connect {$domain}:443 2>/dev/null | openssl x509 -noout -dates");
            
            if ($result->successful()) {
                $output = $result->output();
                preg_match('/notBefore=(.+)/', $output, $beforeMatches);
                preg_match('/notAfter=(.+)/', $output, $afterMatches);
                
                if (isset($beforeMatches[1]) && isset($afterMatches[1])) {
                    $notBefore = new \DateTime(trim($beforeMatches[1]));
                    $notAfter = new \DateTime(trim($afterMatches[1]));
                    $now = new \DateTime();
                    
                    return [
                        'domain' => $domain,
                        'valid_from' => $notBefore->format('Y-m-d H:i:s'),
                        'valid_until' => $notAfter->format('Y-m-d H:i:s'),
                        'days_remaining' => $now->diff($notAfter)->days,
                        'is_valid' => $now >= $notBefore && $now <= $notAfter
                    ];
                }
            }
        } catch (Exception $e) {
            Log::error("Failed to get certificate info for {$domain}: " . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Clear Caddy data directory (certificates, etc.)
     */
    public function clearData(): bool
    {
        try {
            $dataPath = $this->config['data_path'] ?? '/var/lib/caddy';
            $result = Process::run("rm -rf {$dataPath}/*");
            return $result->successful();
        } catch (Exception $e) {
            Log::error('Failed to clear Caddy data: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Backup configuration
     */
    public function backupConfig(): bool
    {
        try {
            $configPath = $this->config['config_path'];
            $backupPath = $configPath . '.backup.' . date('Y-m-d_H-i-s');
            $result = Process::run("cp {$configPath} {$backupPath}");
            return $result->successful();
        } catch (Exception $e) {
            Log::error('Failed to backup Caddy config: ' . $e->getMessage());
            return false;
        }
    }
}
