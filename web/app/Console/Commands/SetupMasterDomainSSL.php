<?php

namespace App\Console\Commands;

use App\Jobs\ApacheBuild;
use App\MasterDomain;
use App\Models\DomainSslCertificate;
use Illuminate\Console\Command;

class SetupMasterDomainSSL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:setup-master-domain-ssl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install SSL certificate for the master domain';

    /**
     * The master domain name.
     *
     * @var string
     */
    protected $masterDomain;

    /**
     * The master email.
     *
     * @var string
     */
    protected $masterEmail;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {

        do {
            $this->askForMasterDomain();
            $changeMasterDomain = $this->confirm('Are you sure you want to proceed with this master domain?', true);
        } while (!$changeMasterDomain);

        do {
            $this->askForMasterEmail();
            $changeMasterEmail = $this->confirm('Are you sure you want to proceed with this master email?', true);
        } while (!$changeMasterEmail);

        setting([
            'general.master_domain' => $this->masterDomain,
            'general.master_email' => $this->masterEmail,
        ]);

        $this->info('Adding ' . $this->masterDomain . ' to Apache 2...');


        $apacheBuild = new ApacheBuild(true);
        $apacheBuild->handle();

        $this->info('Setting up SSL certificate for the master domain...');
        $this->info('This may take a few minutes. Please wait...');

        // Replace default index.html with a new one
        file_put_contents('/var/www/html/index.html', view('actions/samples/apache/html/app-index')->render());

        // Register ACME account
        $acmeCommand = "bash /usr/local/phyre/web/Modules/SSLManager/shell/acme.sh --register-account -m $this->masterEmail";
        $acmeCommand = shell_exec($acmeCommand);

        // Issue SSL certificate
        $acmeCommand = "bash /usr/local/phyre/web/Modules/SSLManager/shell/acme.sh --issue -d '$this->masterDomain' --webroot /var/www/html";
        $acmeCommand = shell_exec($acmeCommand);

        $issued = false;
        if (!str_contains('END CERTIFICATE', $acmeCommand)) {
            $issued = true;
        }
        if (!str_contains('Next renewal time is', $acmeCommand)) {
            $issued = true;
        }
        if (!str_contains('to force renewal', $acmeCommand)) {
            $issued = true;
        }

        if (!$issued) {
            $this->error('Failed to install SSL certificate for the master domain... #1');
            return;
        }

        $sslFiles = $this->_checkCertificateFilesExist($this->masterDomain);
        if (!$sslFiles) {
            $this->error('Failed to install SSL certificate for the master domain... #2');
            return;
        }
        if (!isset($sslFiles['sslFiles']['certificateContent'])) {
            $this->error('Failed to install SSL certificate for the master domain... #3');
            return;
        }

        $findWildcardSsl = DomainSslCertificate::where('domain', $this->masterDomain)->first();
        if (!$findWildcardSsl) {
            $findWildcardSsl = new DomainSslCertificate();
            $findWildcardSsl->domain = $this->masterDomain;
            $findWildcardSsl->customer_id = 0;
            $findWildcardSsl->is_active = 1;
            $findWildcardSsl->is_wildcard = 0;
            $findWildcardSsl->is_auto_renew = 1;
            $findWildcardSsl->provider = 'AUTO_SSL';
        }

        $findWildcardSsl->certificate = $sslFiles['sslFiles']['certificateContent'];
        $findWildcardSsl->private_key = $sslFiles['sslFiles']['privateKeyContent'];
        $findWildcardSsl->certificate_chain = $sslFiles['sslFiles']['certificateChainContent'];
        $findWildcardSsl->save();

        // Replace PhyrePanel Certificate
        file_put_contents('/usr/local/phyre/ssl/phyre.crt', $sslFiles['sslFiles']['certificateContent']);
        file_put_contents('/usr/local/phyre/ssl/phyre.key', $sslFiles['sslFiles']['privateKeyContent']);
        file_put_contents('/usr/local/phyre/ssl/phyre.chain', $sslFiles['sslFiles']['certificateChainContent']);

        // Restart PhyrePanel Service
        shell_exec('service phyre restart');

        $mds = new MasterDomain();
        $mds->configureVirtualHost();

        $apacheBuild = new ApacheBuild(true);
        $apacheBuild->handle();

        $this->info('Everything is set up!');
        $this->info('You can now visit your PhyrePanel at https://' . $this->masterDomain.':8443');

    }

    private function _checkCertificateFilesExist($domain)
    {

        //check file
        $sslCertificateFilePath = '/root/.acme.sh/' . $domain . '_ecc/' . $domain . '.cer';
        $sslCertificateKeyFilePath = '/root/.acme.sh/' . $domain . '_ecc/' . $domain . '.key';
        $sslCertificateChainFilePath = '/root/.acme.sh/' . $domain . '_ecc/fullchain.cer';


        if (file_exists($sslCertificateFilePath)
            && file_exists($sslCertificateKeyFilePath)
            && file_exists($sslCertificateChainFilePath)
        ) {

            $sslCertificateFileContent = file_get_contents($sslCertificateFilePath);
            $sslCertificateKeyFileContent = file_get_contents($sslCertificateKeyFilePath);
            $sslCertificateChainFileContent = file_get_contents($sslCertificateChainFilePath);

            return [
                'sslFiles' => [
                    'certificate' => $sslCertificateFilePath,
                    'certificateContent' => $sslCertificateFileContent,
                    'privateKey' => $sslCertificateKeyFilePath,
                    'privateKeyContent' => $sslCertificateKeyFileContent,
                    'certificateChain' => $sslCertificateChainFilePath,
                    'certificateChainContent' => $sslCertificateChainFileContent
                ]
            ];
        }

        return false;

    }

    public function askForMasterDomain()
    {
        do {
            $this->masterDomain = $this->ask('Enter valid Master Domain name (e.g. myhost.com):');
            if (!$this->isValidDomain($this->masterDomain)) {
                $this->error('Invalid domain name entered. Please try again.');
            }
        } while (!$this->isValidDomain($this->masterDomain));

        $this->info("Master domain: $this->masterDomain");
        $this->info('Your PhyrePanel will be visitable at https://' . $this->masterDomain.':8443');

    }

    public function askForMasterEmail()
    {
        do {
            $this->masterEmail = $this->ask('Enter valid Master email name (e.g. email@myhost.com):');
            if (!$this->isValidEmail($this->masterEmail)) {
                $this->error('Invalid master email entered. Please try again.');
            }
        } while (!$this->isValidEmail($this->masterEmail));

        $this->info("Master email: $this->masterEmail");

    }

    /**
     * Validate the given email.
     *
     * @param string $email
     * @return bool
     */
    private function isValidEmail($email) {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Validate the given domain name.
     *
     * @param string $domain
     * @return bool
     */
    private function isValidDomain($domain)
    {
        return (bool) preg_match('/^(?!-)([a-zA-Z0-9-]{1,63}(?<!-)\.)+[a-zA-Z]{2,}$/', $domain);
    }
}
