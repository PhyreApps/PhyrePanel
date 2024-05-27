<?php

namespace App\Models;

use App\Actions\ApacheWebsiteDelete;
use App\Events\DomainIsCreated;
use App\Events\ModelDomainDeleting;
use App\Jobs\ApacheBuild;
use App\ShellApi;
use App\VirtualHosts\DTO\ApacheVirtualHostSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Modules\Docker\App\Models\DockerContainer;

class Domain extends Model
{

    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_DELETED = 'deleted';
    public const STATUS_DEACTIVATED = 'deactivated';

    public const STATUS_BROKEN = 'broken';

    protected $fillable = [
        'domain',
        'domain_root',
        'ip',
        'hosting_subscription_id',
        'server_application_type',
        'server_application_settings',
        'status'
    ];

    protected $casts = [
        'server_application_settings' => 'array',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('customer', function (Builder $query) {
            if (auth()->check() && auth()->guard()->name == 'web_customer') {
                $query->whereHas('hostingSubscription', function ($query) {
                    $query->where('customer_id', auth()->user()->id);
                });
            }
        });
    }

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {

            $findHostingSubscription = HostingSubscription::where('id', $model->hosting_subscription_id)->first();
            if (! $findHostingSubscription) {
                return;
            }

            $findHostingPlan = HostingPlan::where('id', $findHostingSubscription->hosting_plan_id)->first();
            if (! $findHostingPlan) {
                return;
            }

            $model->server_application_type = $findHostingPlan->default_server_application_type;
            $model->server_application_settings = $findHostingPlan->default_server_application_settings;

            if ($model->is_main == 1) {
              //  $allDomainsRoot = '/home/'.$this->user.'/public_html';
                $model->domain_root = '/home/'.$findHostingSubscription->system_username;
                $model->domain_public = '/home/'.$findHostingSubscription->system_username.'/public_html';
                $model->home_root = '/home/'.$findHostingSubscription->system_username;
            } else {
             //   $allDomainsRoot = '/home/'.$model->user.'/domains';
                $model->domain_root = '/home/'.$findHostingSubscription->system_username.'/domains/'.$model->domain;
                $model->domain_public = $model->domain_root.'/public_html';
                $model->home_root = '/home/'.$findHostingSubscription->user;
            }
            $model->saveQuietly();

            $model->configureVirtualHost(true);

            event(new DomainIsCreated($model));

        });

        static::saved(function ($model) {

            $model->configureVirtualHost(true);

            ApacheBuild::dispatchSync();

        });

        static::deleting(function ($model) {

            if (empty($model->domain_public)) {
                return;
            }

            if ($model->is_main == 1) {
                $findHostingSubscription = HostingSubscription::where('id', $model->hosting_subscription_id)->first();
                if (!$findHostingSubscription) {
                    return;
                }
                ShellApi::safeDelete($model->domain_root, ['/home/' . $findHostingSubscription->system_username]);
            }

        });

        static::deleted(function ($model) {

            ApacheBuild::dispatchSync();

        });

    }

    public function hostingSubscription()
    {
        return $this->belongsTo(HostingSubscription::class);
    }

    public function configureVirtualHost($fixPermissions = false)
    {
        $findHostingSubscription = \App\Models\HostingSubscription::where('id', $this->hosting_subscription_id)
            ->first();
        if (!$findHostingSubscription) {
            throw new \Exception('Hosting subscription not found');
        }

        $findHostingPlan = \App\Models\HostingPlan::where('id', $findHostingSubscription->hosting_plan_id)
            ->first();
        if (!$findHostingPlan) {
            throw new \Exception('Hosting plan not found');
        }

        if (empty($this->domain_root)) {
            throw new \Exception('Domain root not found');
        }

        if ($fixPermissions) {
            if (!is_dir($this->domain_root)) {
                mkdir($this->domain_root, 0711, true);
            }
            if (!is_dir($this->domain_public)) {
                mkdir($this->domain_public, 0755, true);
            }
            if (!is_dir($this->home_root)) {
                mkdir($this->home_root, 0711, true);
            }
        }

        if ($this->is_installed_default_app_template == null) {
            $this->is_installed_default_app_template = 1;
            $this->saveQuietly();
            if ($this->server_application_type == 'apache_php') {
                if (!is_file($this->domain_public . '/index.php')) {
                    $indexContent = view('actions.samples.apache.php.app-php-sample')->render();
                    file_put_contents($this->domain_public . '/index.php', $indexContent);
                }
                if (!is_dir($this->domain_public . '/templates')) {
                    mkdir($this->domain_public . '/templates', 0755, true);
                }
                if (!is_file($this->domain_public . '/templates/index.html')) {
                    $indexContent = view('actions.samples.apache.php.app-index-html')->render();
                    file_put_contents($this->domain_public . '/templates/index.html', $indexContent);
                }
            }

            if ($this->server_application_type == 'apache_nodejs') {
                if (!is_file($this->domain_public . '/app.js')) {
                    $indexContent = view('actions.samples.apache.nodejs.app-nodejs-sample')->render();
                    file_put_contents($this->domain_public . '/app.js', $indexContent);
                }
                if (!is_dir($this->domain_public . '/templates')) {
                    mkdir($this->domain_public . '/templates', 0755, true);
                }
                if (!is_file($this->domain_public . '/templates/index.html')) {
                    $indexContent = view('actions.samples.apache.nodejs.app-index-html')->render();
                    file_put_contents($this->domain_public . '/templates/index.html', $indexContent);
                }
            }

            if ($this->server_application_type == 'apache_python') {
                if (!is_file($this->domain_public . '/app.py')) {
                    $indexContent = view('actions.samples.apache.python.app-python-sample')->render();
                    file_put_contents($this->domain_public . '/app.py', $indexContent);
                }
                if (!is_file($this->domain_public . '/passenger_wsgi.py')) {
                    $indexContent = view('actions.samples.apache.python.app-passanger-wsgi-sample')->render();
                    file_put_contents($this->domain_public . '/passenger_wsgi.py', $indexContent);
                }
                if (!is_dir($this->domain_public . '/templates')) {
                    mkdir($this->domain_public . '/templates', 0755, true);
                }
                if (!is_file($this->domain_public . '/templates/index.html')) {
                    $indexContent = view('actions.samples.apache.python.app-index-html')->render();
                    file_put_contents($this->domain_public . '/templates/index.html', $indexContent);
                }
            }
        }

        $webUserGroup = $findHostingSubscription->system_username;

        if ($fixPermissions) {
            // Fix file permissions
            shell_exec('chown -R ' . $findHostingSubscription->system_username . ':' . $webUserGroup . ' ' . $this->home_root);
            shell_exec('chown -R ' . $findHostingSubscription->system_username . ':' . $webUserGroup . ' ' . $this->domain_root);
            shell_exec('chown -R ' . $findHostingSubscription->system_username . ':' . $webUserGroup . ' ' . $this->domain_public);

            shell_exec('chmod -R 0711 ' . $this->home_root);
            shell_exec('chmod -R 0711 ' . $this->domain_root);
            shell_exec('chmod -R 775 ' . $this->domain_public);

            if (!is_dir($this->domain_root . '/logs/apache2')) {
                shell_exec('mkdir -p ' . $this->domain_root . '/logs/apache2');
            }
            shell_exec('chown -R ' . $findHostingSubscription->system_username . ':' . $webUserGroup . ' ' . $this->domain_root . '/logs/apache2');
            shell_exec('chmod -R 775 ' . $this->domain_root . '/logs/apache2');

            if (!is_file($this->domain_root . '/logs/apache2/bytes.log')) {
                shell_exec('touch ' . $this->domain_root . '/logs/apache2/bytes.log');
            }
            if (!is_file($this->domain_root . '/logs/apache2/access.log')) {
                shell_exec('touch ' . $this->domain_root . '/logs/apache2/access.log');
            }
            if (!is_file($this->domain_root . '/logs/apache2/error.log')) {
                shell_exec('touch ' . $this->domain_root . '/logs/apache2/error.log');
            }

            shell_exec('chmod -R 775 ' . $this->domain_root . '/logs/apache2/bytes.log');
            shell_exec('chmod -R 775 ' . $this->domain_root . '/logs/apache2/access.log');
            shell_exec('chmod -R 775 ' . $this->domain_root . '/logs/apache2/error.log');
        }

        $appType = 'php';
        $appVersion = '8.3';

        if ($this->server_application_type == 'apache_php') {
            if (isset($this->server_application_settings['php_version'])) {
                $appVersion = $this->server_application_settings['php_version'];
            }
            if (!is_dir($this->domain_public . '/cgi-bin')) {
                mkdir($this->domain_public . '/cgi-bin', 0755, true);
            }
            file_put_contents($this->domain_public . '/cgi-bin/php', '#!/usr/bin/php-cgi' . $appVersion . ' -cphp' . $appVersion . '-cgi.ini');
            shell_exec('chown '.$findHostingSubscription->system_username.':'.$webUserGroup.' '.$this->domain_public . '/cgi-bin/php');
            shell_exec('chmod -f 751 '.$this->domain_public . '/cgi-bin/php');
        }

        $apacheVirtualHostBuilder = new ApacheVirtualHostSettings();
        $apacheVirtualHostBuilder->setDomain($this->domain);
        $apacheVirtualHostBuilder->setDomainPublic($this->domain_public);
        $apacheVirtualHostBuilder->setDomainRoot($this->domain_root);
        $apacheVirtualHostBuilder->setHomeRoot($this->home_root);
        $apacheVirtualHostBuilder->setUser($findHostingSubscription->system_username);
        $apacheVirtualHostBuilder->setUserGroup($webUserGroup);

        if ($this->status == self::STATUS_SUSPENDED) {
            $suspendedPath = '/var/www/html/suspended';
            if (!is_dir($suspendedPath)) {
                mkdir($suspendedPath, 0755, true);
            }
            if (!is_file($suspendedPath . '/index.html')) {
                $suspendedPageHtmlPath = base_path('resources/views/actions/samples/apache/html/app-suspended-page.html');
                file_put_contents($suspendedPath . '/index.html', file_get_contents($suspendedPageHtmlPath));
            }
            $apacheVirtualHostBuilder->setDomainRoot($suspendedPath);
            $apacheVirtualHostBuilder->setDomainPublic($suspendedPath);
        } else if ($this->status == self::STATUS_DEACTIVATED) {
            $deactivatedPath = '/var/www/html/deactivated';
            if (!is_dir($deactivatedPath)) {
                mkdir($deactivatedPath, 0755, true);
            }
            if (!is_file($deactivatedPath . '/index.html')) {
                $deactivatedPageHtmlPath = base_path('resources/views/actions/samples/apache/html/app-deactivated-page.html');
                file_put_contents($deactivatedPath . '/index.html', file_get_contents($deactivatedPageHtmlPath));
            }
            $apacheVirtualHostBuilder->setDomainRoot($deactivatedPath);
            $apacheVirtualHostBuilder->setDomainPublic($deactivatedPath);
        } else if ($this->status == self::STATUS_BROKEN) {
            $brokenPath = '/var/www/html/broken';
            if (!is_dir($brokenPath)) {
                mkdir($brokenPath, 0755, true);
            }
            if (!is_file($brokenPath . '/index.html')) {
                $brokenPageHtmlPath = base_path('resources/views/actions/samples/apache/html/app-broken-page.html');
                file_put_contents($brokenPath . '/index.html', file_get_contents($brokenPageHtmlPath));
            }
            $apacheVirtualHostBuilder->setDomainRoot($brokenPath);
            $apacheVirtualHostBuilder->setDomainPublic($brokenPath);
        } else {

          //  $apacheVirtualHostBuilder->setEnableLogs(true);
            $apacheVirtualHostBuilder->setAdditionalServices($findHostingPlan->additional_services);
            $apacheVirtualHostBuilder->setAppType($appType);
            $apacheVirtualHostBuilder->setAppVersion($appVersion);

            if ($this->server_application_type == 'apache_nodejs') {
                $apacheVirtualHostBuilder->setAppType('nodejs');
                $apacheVirtualHostBuilder->setPassengerAppRoot($this->domain_public);
                $apacheVirtualHostBuilder->setPassengerAppType('node');
                $apacheVirtualHostBuilder->setPassengerStartupFile('app.js');

                if (isset($this->server_application_settings['nodejs_version'])) {
                    $apacheVirtualHostBuilder->setAppVersion($this->server_application_settings['nodejs_version']);
                }
            }

            if ($this->server_application_type == 'apache_python') {
                $apacheVirtualHostBuilder->setAppType('python');
                $apacheVirtualHostBuilder->setPassengerAppRoot($this->domain_public);
                $apacheVirtualHostBuilder->setPassengerAppType('python');

                if (isset($this->server_application_settings['python_version'])) {
                    $apacheVirtualHostBuilder->setAppVersion($this->server_application_settings['python_version']);
                }
            }

            if ($this->server_application_type == 'apache_ruby') {
                $apacheVirtualHostBuilder->setAppType('ruby');
                $apacheVirtualHostBuilder->setPassengerAppRoot($this->domain_public);
                $apacheVirtualHostBuilder->setPassengerAppType('ruby');

                if (isset($this->server_application_settings['ruby_version'])) {
                    $apacheVirtualHostBuilder->setAppVersion($this->server_application_settings['ruby_version']);
                }

            }
            if ($this->server_application_type == 'apache_docker') {
                if (isset($this->server_application_settings['docker_container_id'])) {
                    $findDockerContainer = DockerContainer::where('id', $this->server_application_settings['docker_container_id'])
                        ->first();
                    if ($findDockerContainer) {
                        $apacheVirtualHostBuilder->setProxyPass('http://127.0.0.1:' . $findDockerContainer->external_port . '/');
                        $apacheVirtualHostBuilder->setAppType('docker');
                        $apacheVirtualHostBuilder->setAppVersion($appVersion);
                    }
                }
            }
        }

        $virtualHostSettings = $apacheVirtualHostBuilder->getSettings();

        $catchMainDomain = '';
        $domainExp = explode('.', $this->domain);
        if (count($domainExp) > 0) {
            unset($domainExp[0]);
            $catchMainDomain = implode('.', $domainExp);
        }

        $findDomainSSLCertificate = null;

        $findMainDomainSSLCertificate = \App\Models\DomainSslCertificate::where('domain', $this->domain)
            ->first();
        if ($findMainDomainSSLCertificate) {
            $findDomainSSLCertificate = $findMainDomainSSLCertificate;
        } else {
            $findDomainSSLCertificateWildcard = \App\Models\DomainSslCertificate::where('domain', '*.' . $this->domain)
                ->where('is_wildcard', 1)
                ->first();
            if ($findDomainSSLCertificateWildcard) {
                $findDomainSSLCertificate = $findDomainSSLCertificateWildcard;
            } else {
                $findMainDomainWildcardSSLCertificate = \App\Models\DomainSslCertificate::where('domain', '*.'.$catchMainDomain)
                    ->first();
                if ($findMainDomainWildcardSSLCertificate) {
                    $findDomainSSLCertificate = $findMainDomainWildcardSSLCertificate;
                }
            }
        }

        $virtualHostSettingsWithSSL = null;
        if ($findDomainSSLCertificate) {

            $sslCertificateFile = $this->home_root . '/certs/' . $this->domain . '/public/cert.pem';
            $sslCertificateKeyFile = $this->home_root . '/certs/' . $this->domain . '/private/key.private.pem';
            $sslCertificateChainFile = $this->home_root . '/certs/' . $this->domain . '/public/fullchain.pem';

            if (!empty($findDomainSSLCertificate->certificate)) {
                if (!file_exists($sslCertificateFile)) {
                    if (!is_dir($this->home_root . '/certs/' . $this->domain . '/public')) {
                        mkdir($this->home_root . '/certs/' . $this->domain . '/public', 0755, true);
                    }
                    file_put_contents($sslCertificateFile, $findDomainSSLCertificate->certificate);
                }
            }

            if (!empty($findDomainSSLCertificate->private_key)) {
                if (!file_exists($sslCertificateKeyFile)) {
                    if (!is_dir($this->home_root . '/certs/' . $this->domain . '/private')) {
                        mkdir($this->home_root . '/certs/' . $this->domain . '/private', 0755, true);
                    }
                    file_put_contents($sslCertificateKeyFile, $findDomainSSLCertificate->private_key);
                }
            }

            if (!empty($findDomainSSLCertificate->certificate_chain)) {
                if (!file_exists($sslCertificateChainFile)) {
                    if (!is_dir($this->home_root . '/certs/' . $this->domain . '/public')) {
                        mkdir($this->home_root . '/certs/' . $this->domain . '/public', 0755, true);
                    }
                    file_put_contents($sslCertificateChainFile, $findDomainSSLCertificate->certificate_chain);
                }
            }

            $apacheVirtualHostBuilder->setPort(443);
            $apacheVirtualHostBuilder->setSSLCertificateFile($sslCertificateFile);
            $apacheVirtualHostBuilder->setSSLCertificateKeyFile($sslCertificateKeyFile);
            $apacheVirtualHostBuilder->setSSLCertificateChainFile($sslCertificateChainFile);

            $virtualHostSettingsWithSSL = $apacheVirtualHostBuilder->getSettings();

        }

        return [
            'virtualHostSettings' => $virtualHostSettings,
            'virtualHostSettingsWithSSL' => $virtualHostSettingsWithSSL,
        ];

    }
}
