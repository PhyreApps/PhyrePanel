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
    }    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Check if Caddy is enabled
        $caddyEnabled = setting('caddy.enabled') ?? false;
        if (!$caddyEnabled) {
            return;
        }

        // Auto-configure Apache if enabled
        if (setting('caddy.auto_configure_apache', true)) {
            $this->configureApacheForCaddy();
        }

        $getAllDomains = Domain::whereNot('status', '<=>', 'broken')->get();
        $caddyBlocks = [];

        // Get Apache port settings (non-SSL ports for proxying)
        $apacheHttpPort = setting('caddy.apache_proxy_port') ?? setting('general.apache_http_port') ?? '8080';
        $caddyEmail = setting('caddy.email') ?? setting('general.master_email') ?? 'admin@localhost';

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
            'is_master' => true,        ];
    }

    /**
     * Configure Apache to work optimally with Caddy
     */
    private function configureApacheForCaddy(): void
    {
        $apacheProxyPort = setting('caddy.apache_proxy_port', '8080');
        
        // Set Apache to use non-standard HTTP port
        if (setting('general.apache_http_port') != $apacheProxyPort) {
            setting(['general.apache_http_port' => $apacheProxyPort]);
        }
        
        // Disable Apache SSL if enabled
        if (setting('caddy.disable_apache_ssl', true)) {
            setting(['general.apache_ssl_disabled' => true]);
        }
        
        // Rebuild Apache configuration with new settings
        \App\Jobs\ApacheBuild::dispatch();
    }
}
