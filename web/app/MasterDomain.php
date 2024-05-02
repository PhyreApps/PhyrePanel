<?php

namespace App;

class MasterDomain
{
    public $domain;

    public $email;

    public $country = 'US';

    public $locality = 'New York';

    public $organization = 'PhyrePanel';

    public $domainPublic = '/var/www/html';

    public $domainRoot = '/var/www';

    public function __construct()
    {
        $generalSettings = Settings::general();

        $this->domain = $generalSettings['master_domain'];
        $this->email = $generalSettings['master_email'];
        $this->country = $generalSettings['master_country'];
        $this->locality = $generalSettings['master_locality'];
        $this->organization = $generalSettings['organization_name'];

    }

    public function configureVirtualHost()
    {
        // check is valid domain
        if (!filter_var($this->domain, FILTER_VALIDATE_DOMAIN)) {
            return false;
        }

        $apacheVirtualHostBuilder = new \App\VirtualHosts\ApacheVirtualHostBuilder();
        $apacheVirtualHostBuilder->setDomain($this->domain);
        $apacheVirtualHostBuilder->setDomainPublic($this->domainPublic);
        $apacheVirtualHostBuilder->setDomainRoot($this->domainRoot);
        $apacheVirtualHostBuilder->setHomeRoot($this->domainRoot);
        $apacheVirtualHostBuilder->setServerAdmin($this->email);

        $apacheBaseConfig = $apacheVirtualHostBuilder->buildConfig();

        shell_exec('mkdir -p /var/www/logs/apache2');
        shell_exec('touch /var/www/logs/apache2/bytes.log');
        shell_exec('touch /var/www/logs/apache2/access.log');
        shell_exec('touch /var/www/logs/apache2/error.log');

        if (!empty($apacheBaseConfig)) {
            file_put_contents('/etc/apache2/sites-available/'.$this->domain.'.conf', $apacheBaseConfig);
            shell_exec('ln -s /etc/apache2/sites-available/'.$this->domain.'-default.conf /etc/apache2/sites-enabled/'.$this->domain.'-default.conf');
        }

        // install SSL
        $findDomainSSLCertificate = null;

        // Try to find wildcard SSL certificate
        $findDomainSSLCertificateWildcard = \App\Models\DomainSslCertificate::where('domain', '*.' . $this->domain)
            ->where('is_wildcard', 1)
            ->first();
        if ($findDomainSSLCertificateWildcard) {
            $findDomainSSLCertificate = $findDomainSSLCertificateWildcard;
        } else {
            $findDomainSSL = \App\Models\DomainSslCertificate::where('domain', $this->domain)->first();
            $findDomainSSLCertificate = $findDomainSSL;
        }

        if ($findDomainSSLCertificate) {

            $certsFolderName = $findDomainSSLCertificate->domain;
            $certsFolderName = str_replace('*.', 'wildcard.', $certsFolderName);

            $sslCertificateFile = $this->domainRoot . '/certs/' . $certsFolderName . '/public/cert.pem';
            $sslCertificateKeyFile = $this->domainRoot . '/certs/' . $certsFolderName . '/private/key.private.pem';
            $sslCertificateChainFile = $this->domainRoot . '/certs/' . $certsFolderName . '/public/fullchain.pem';

            if (!empty($findDomainSSLCertificate->certificate)) {
                if (!is_dir($this->domainRoot . '/certs/' . $certsFolderName . '/public')) {
                    mkdir($this->domainRoot . '/certs/' . $certsFolderName . '/public', 0755, true);
                }
                file_put_contents($sslCertificateFile, $findDomainSSLCertificate->certificate);
            }
            if (!empty($findDomainSSLCertificate->private_key)) {
                if (!is_dir($this->domainRoot . '/certs/' . $certsFolderName . '/private')) {
                    mkdir($this->domainRoot . '/certs/' . $certsFolderName . '/private', 0755, true);
                }
                file_put_contents($sslCertificateKeyFile, $findDomainSSLCertificate->private_key);
            }
            if (!empty($findDomainSSLCertificate->certificate_chain)) {
                if (!is_dir($this->domainRoot . '/certs/' . $certsFolderName . '/public')) {
                    mkdir($this->domainRoot . '/certs/' . $certsFolderName . '/public', 0755, true);
                }
                file_put_contents($sslCertificateChainFile, $findDomainSSLCertificate->certificate_chain);
            }

            $apacheVirtualHostBuilder->setPort(443);
            $apacheVirtualHostBuilder->setSSLCertificateFile($sslCertificateFile);
            $apacheVirtualHostBuilder->setSSLCertificateKeyFile($sslCertificateKeyFile);
            $apacheVirtualHostBuilder->setSSLCertificateChainFile($sslCertificateChainFile);

            $apacheBaseConfigWithSSL = $apacheVirtualHostBuilder->buildConfig();
            if (!empty($apacheBaseConfigWithSSL)) {

                // Add SSL options conf file
                $apache2SSLOptionsSample = view('actions.samples.ubuntu.apache2-ssl-options-conf')->render();
                $apache2SSLOptionsFilePath = '/etc/apache2/phyre/options-ssl-apache.conf';

                if (!file_exists($apache2SSLOptionsFilePath)) {
                    if (!is_dir('/etc/apache2/phyre')) {
                        mkdir('/etc/apache2/phyre');
                    }
                    file_put_contents($apache2SSLOptionsFilePath, $apache2SSLOptionsSample);
                }

                file_put_contents('/etc/apache2/sites-available/'.$this->domain.'-ssl.conf', $apacheBaseConfigWithSSL);
                shell_exec('ln -s /etc/apache2/sites-available/'.$this->domain.'-ssl.conf /etc/apache2/sites-enabled/'.$this->domain.'-ssl.conf');

            }

        }
        // End install SSL

        $domainIndexFile = $this->domainPublic . '/index.html';
        if (file_exists($domainIndexFile)) {
            $domainIndexFileContent = file_get_contents($domainIndexFile);
            if (str_contains($domainIndexFileContent, 'Apache2 Debian Default Page')) {
                 $indexContent = file_get_contents(base_path('resources/views/actions/samples/apache/html/app-index.html'));
                 file_put_contents($this->domainPublic . '/index.html', $indexContent);
            }
        }

        shell_exec('chown -R www-data:www-data ' . $this->domainPublic);
        shell_exec('chmod -R 755 ' . $this->domainPublic);
        shell_exec('systemctl restart apache2');
    }
}
