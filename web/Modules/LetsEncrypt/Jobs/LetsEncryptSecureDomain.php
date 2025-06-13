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

        if(setting('caddy.enabled')){
            // Caddy is enabled, skip Let's Encrypt
            return;
        }


        $findDomain = \App\Models\Domain::where('id', $this->domainId)->first();
        if (!$findDomain) {
            throw new \Exception('Domain not found');
        }
        $domainName = $findDomain->domain;

        $domainName = trim($domainName);
        $domainName = str_replace('www.', '', $domainName);
        if (empty($domainName)) {
            throw new \Exception('Domain name is empty');
        }
        $domainNameWww = 'www.' . $domainName;
        $domainNameWww = str_replace('www.www.', 'www.', $domainNameWww);


        $generalSettings = Settings::general();

        $sslCertificateFilePath = '/etc/letsencrypt/live/' . $domainName . '/cert.pem';
        $sslCertificateKeyFilePath = '/etc/letsencrypt/live/' . $domainName . '/privkey.pem';
        $sslCertificateChainFilePath = '/etc/letsencrypt/live/' . $domainName . '/fullchain.pem';


        $certbotHttpSecureCommand = view('letsencrypt::actions.certbot-http-secure-command', [
            'domain' => $domainName,
            'domainNameWww' => $domainNameWww,
            'domainRoot' => $findDomain->domain_root,
            'domainPublic' => $findDomain->domain_public,
            'sslCertificateFilePath' => $sslCertificateFilePath,
            'sslCertificateKeyFilePath' => $sslCertificateKeyFilePath,
            'sslCertificateChainFilePath' => $sslCertificateChainFilePath,
            'email' => $generalSettings['master_email'],
            'country' => $generalSettings['master_country'],
            'locality' => $generalSettings['master_locality'],
            'organization' => $generalSettings['organization_name'],
        ])->render();

        $isCertbotInstalled = shell_exec('which certbot');
        if (empty($isCertbotInstalled)) {
            shell_exec('sudo apt install certbot -y');
        }


        //delete cert
        //certbot delete --cert-name example.com
        shell_exec('certbot delete --cert-name ' . $domainName . ' -n');


        $output = '';
        $tmpFile = '/tmp/certbot-http-secure-command-' . $findDomain->id . '.sh';

        file_put_contents($tmpFile, $certbotHttpSecureCommand);
        shell_exec('chmod +x ' . $tmpFile);
        shell_exec('chmod +x /usr/local/phyre/web/Modules/LetsEncrypt/shell/hooks/pre/http-authenticator.sh');
        shell_exec('chmod +x /usr/local/phyre/web/Modules/LetsEncrypt/shell/hooks/post/http-cleanup.sh');
        shell_exec('chmod +x /usr/local/phyre/web/Modules/LetsEncrypt/shell/acme.sh');

        $exec = shell_exec("bash $tmpFile");

        sleep(10);
        shell_exec('chmod 0755 /etc/letsencrypt/live/' . $domainName . '/privkey.pem');
        shell_exec('chmod 0755 /etc/letsencrypt/live/' . $domainName . '/fullchain.pem');
        shell_exec('chmod 0755 /etc/letsencrypt/live/' . $domainName . '/cert.pem');
        shell_exec('chmod 0755 /etc/letsencrypt/live/' . $domainName . '/chain.pem');

        unlink($tmpFile);

        $validateCertificates = [];


        if (!file_exists($sslCertificateFilePath)
            || !file_exists($sslCertificateKeyFilePath)
            || !file_exists($sslCertificateChainFilePath)) {
            // Cant get all certificates
            // fallback to zerossl via acme,sh
            // fallback to zerossl via acme,sh
            // fallback to zerossl via acme,sh
            //acme.sh  --register-account  -m myemail@example.com --server zerossl
            $exec = shell_exec("bash /usr/local/phyre/web/Modules/LetsEncrypt/shell/acme.sh  --register-account  -m " . $generalSettings['master_email'] . " --server zerossl");

            $tmpFile = '/tmp/acme-sh-zerossl-http-secure-command-' . $findDomain->id . '.sh';
            $certbotHttpSecureCommand = view('letsencrypt::actions.acme-sh-http-secure-command', [
                'domain' => $domainName,
                'domainNameWww' => $domainNameWww,
                'domainRoot' => $findDomain->domain_root,
                'domainPublic' => $findDomain->domain_public,
                'sslCertificateFilePath' => $sslCertificateFilePath,
                'sslCertificateKeyFilePath' => $sslCertificateKeyFilePath,
                'sslCertificateChainFilePath' => $sslCertificateChainFilePath,
                'email' => $generalSettings['master_email'],
                'country' => $generalSettings['master_country'],
                'locality' => $generalSettings['master_locality'],
                'organization' => $generalSettings['organization_name'],
            ])->render();
            file_put_contents($tmpFile, $certbotHttpSecureCommand);
            shell_exec('chmod +x ' . $tmpFile);
            $exec = shell_exec("bash $tmpFile");
            unlink($tmpFile);

            //check file
            $zerSslCert = '/root/.acme.sh/' . $domainName . '_ecc/' . $domainName . '.cer';
            $zerSslCertKey = '/root/.acme.sh/' . $domainName . '_ecc/' . $domainName . '.key';
            $zerSslCertIntermediate = '/root/.acme.sh/' . $domainName . '_ecc/ca.cer';
            $zerSslCertFullChain = '/root/.acme.sh/' . $domainName . '_ecc/fullchain.cer';


            //try without www
            if (!file_exists($zerSslCert)
                || !file_exists($zerSslCertKey)
                || !file_exists($zerSslCertFullChain)) {
                $tmpFile = '/tmp/acme-sh-zerossl-http-secure-command-' . $findDomain->id . '.sh';
                $certbotHttpSecureCommand = view('sslmanager::actions.acme-sh-http-secure-command', [
                    'domain' => $domainName,
                    //    'domainNameWww' => $domainNameWww,
                    'domainRoot' => $findDomain->domain_root,
                    'domainPublic' => $findDomain->domain_public,
                    'email' => $generalSettings['master_email'],
                    'country' => $generalSettings['master_country'],
                    'locality' => $generalSettings['master_locality'],
                    'organization' => $generalSettings['organization_name'],
                ])->render();

                file_put_contents($tmpFile, $certbotHttpSecureCommand);
                shell_exec('chmod +x ' . $tmpFile);

                $exec = shell_exec("bash $tmpFile");
                unlink($tmpFile);

                //check file
                $zerSslCert = '/root/.acme.sh/' . $domainName . '_ecc/' . $domainName . '.cer';
                $zerSslCertKey = '/root/.acme.sh/' . $domainName . '_ecc/' . $domainName . '.key';
                $zerSslCertIntermediate = '/root/.acme.sh/' . $domainName . '_ecc/ca.cer';
                $zerSslCertFullChain = '/root/.acme.sh/' . $domainName . '_ecc/fullchain.cer';
            }







            if (!file_exists($zerSslCert)
                || !file_exists($zerSslCertKey)
                || !file_exists($zerSslCertFullChain)) {
                // Cant get all certificates
                throw new \Exception('Cant get certificates with ZeroSSL');
            }
            if(!is_dir('/etc/letsencrypt/live/' . $domainName)){
                shell_exec('mkdir -p /etc/letsencrypt/live/' . $domainName);
            }

            //copy to letsencrypt
            file_put_contents($sslCertificateFilePath, file_get_contents($zerSslCert));
            file_put_contents($sslCertificateKeyFilePath, file_get_contents($zerSslCertKey));
            file_put_contents($sslCertificateChainFilePath, file_get_contents($zerSslCertFullChain));

        }





        if (!file_exists($sslCertificateFilePath)
            || !file_exists($sslCertificateKeyFilePath)
            || !file_exists($sslCertificateChainFilePath)) {
            // Cant get all certificates
            throw new \Exception('Cant get all certificates');
        }

        $sslCertificateFileContent = file_get_contents($sslCertificateFilePath);
        $sslCertificateKeyFileContent = file_get_contents($sslCertificateKeyFilePath);
        $sslCertificateChainFileContent = file_get_contents($sslCertificateChainFilePath);

        if (!empty($sslCertificateChainFileContent)) {
            $validateCertificates['certificate'] = $sslCertificateFileContent;
        }
        if (!empty($sslCertificateKeyFileContent)) {
            $validateCertificates['private_key'] = $sslCertificateKeyFileContent;
        }
        if (!empty($sslCertificateChainFileContent)) {
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
