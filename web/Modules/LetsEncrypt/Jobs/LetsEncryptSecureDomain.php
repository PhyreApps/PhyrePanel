<?php

namespace Modules\LetsEncrypt\Jobs;

use App\Models\DomainSslCertificate;
use App\Settings;

class LetsEncryptSecureDomain
{

    public $domainId;

    public function __construct($domainId)
    {
        $this->domainId = $domainId;
    }

    public function handle(): void
    {

        $findDomain = \App\Models\Domain::where('id', $this->domainId)->first();
        if (! $findDomain) {
            throw new \Exception('Domain not found');
        }

        $generalSettings = Settings::general();

        $sslCertificateFilePath = '/etc/letsencrypt/live/'.$findDomain->domain.'/cert.pem';
        $sslCertificateKeyFilePath = '/etc/letsencrypt/live/'.$findDomain->domain.'/privkey.pem';
        $sslCertificateChainFilePath = '/etc/letsencrypt/live/'.$findDomain->domain.'/fullchain.pem';

        $certbotHttpSecureCommand = view('letsencrypt::actions.certbot-http-secure-command', [
            'domain' => $findDomain->domain,
            'domainRoot' => $findDomain->domain_root,
            'domainPublic' => $findDomain->domain_public,
            'email' => $generalSettings['master_email'],
            'country' => $generalSettings['master_country'],
            'locality' => $generalSettings['master_locality'],
            'organization' => $generalSettings['organization_name'],
        ])->render();

        $tmpFile = '/tmp/certbot-http-secure-command-'.$findDomain->id.'.sh';
        file_put_contents($tmpFile, $certbotHttpSecureCommand);
        shell_exec('chmod +x '.$tmpFile);
        shell_exec('chmod +x /usr/local/phyre/web/Modules/LetsEncrypt/shell/hooks/pre/http-authenticator.sh');
        shell_exec('chmod +x /usr/local/phyre/web/Modules/LetsEncrypt/shell/hooks/post/http-cleanup.sh');
        $exec = shell_exec("bash $tmpFile");

        unlink($tmpFile);

        $validateCertificates = [];

        if (! file_exists($sslCertificateFilePath)
            || ! file_exists($sslCertificateKeyFilePath)
            || ! file_exists($sslCertificateChainFilePath)) {
            // Cant get all certificates
            throw new \Exception('Cant get all certificates');
        }

        $sslCertificateFileContent = file_get_contents($sslCertificateFilePath);
        $sslCertificateKeyFileContent = file_get_contents($sslCertificateKeyFilePath);
        $sslCertificateChainFileContent = file_get_contents($sslCertificateChainFilePath);

        if (! empty($sslCertificateChainFileContent)) {
            $validateCertificates['certificate'] = $sslCertificateFileContent;
        }
        if (! empty($sslCertificateKeyFileContent)) {
            $validateCertificates['private_key'] = $sslCertificateKeyFileContent;
        }
        if (! empty($sslCertificateChainFileContent)) {
            $validateCertificates['certificate_chain'] = $sslCertificateChainFileContent;
        }
        if (count($validateCertificates) !== 3) {
            // Cant get all certificates
            throw new \Exception('Cant get all certificates');
        }

        $websiteSslCertificate = new DomainSslCertificate();
        $websiteSslCertificate->domain = $findDomain->domain;
        $websiteSslCertificate->certificate = $validateCertificates['certificate'];
        $websiteSslCertificate->private_key = $validateCertificates['private_key'];
        $websiteSslCertificate->certificate_chain = $validateCertificates['certificate_chain'];
        $websiteSslCertificate->customer_id = $findDomain->customer_id;
        $websiteSslCertificate->is_active = 1;
        $websiteSslCertificate->is_wildcard = 0;
        $websiteSslCertificate->is_auto_renew = 1;
        $websiteSslCertificate->provider = 'letsencrypt';
        $websiteSslCertificate->save();

        $findDomain->configureVirtualHost(true);

    }
}
