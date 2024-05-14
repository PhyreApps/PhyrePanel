<?php

namespace Modules\LetsEncrypt\Listeners;

use App\ApiClient;
use App\Events\DomainIsCreated;
use App\Models\DomainSslCertificate;
use App\Models\HostingPlan;
use App\Models\HostingSubscription;
use App\Settings;
use App\ShellApi;

class DomainIsCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DomainIsCreated $event): void
    {
        $findDomain = \App\Models\Domain::where('id', $event->model->id)->first();
        if (! $findDomain) {
            return;
        }

        $findHostingSubscription = HostingSubscription::where('id', $findDomain->hosting_subscription_id)->first();
        if (! $findHostingSubscription) {
            return;
        }
        $findHostingPlan = HostingPlan::where('id', $findHostingSubscription->hosting_plan_id)->first();
        if (! $findHostingPlan) {
            return;
        }

        if (! in_array('letsencrypt', $findHostingPlan->additional_services)) {
            return;
        }

        $generalSettings = Settings::general();

        $acmeConfigYaml = view('letsencrypt::actions.acme-config-yaml', [
            'domain' => $event->model->domain,
            'domainRoot' => $event->model->domain_root,
            'domainPublic' => $event->model->domain_public,
            'email' => $generalSettings['master_email'],
            'country' => $generalSettings['master_country'],
            'locality' => $generalSettings['master_locality'],
            'organization' => $generalSettings['organization_name'],
        ])->render();

        file_put_contents($event->model->domain_root.'/acme-config.yaml', $acmeConfigYaml);

        $amePHPPharFile = base_path().'/Modules/LetsEncrypt/Actions/acmephp.phar';

        $phyrePHP = ApiClient::getPhyrePHP();

        $command = $phyrePHP.' '.$amePHPPharFile.' run '.$event->model->domain_root.'/acme-config.yaml';
        $execSSL = shell_exec($command);

        $validateCertificates = [];
        $sslCertificateFilePath = '/root/.acmephp/master/certs/'.$event->model->domain.'/public/cert.pem';
        $sslCertificateKeyFilePath = '/root/.acmephp/master/certs/'.$event->model->domain.'/private/key.private.pem';
        $sslCertificateChainFilePath = '/root/.acmephp/master/certs/'.$event->model->domain.'/public/fullchain.pem';

        if (! file_exists($sslCertificateFilePath)
            || ! file_exists($sslCertificateKeyFilePath)
            || ! file_exists($sslCertificateChainFilePath)) {
            // Cant get all certificates
            return;
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
            return;
        }

        $websiteSslCertificate = new DomainSslCertificate();
        $websiteSslCertificate->domain = $event->model->domain;
        $websiteSslCertificate->certificate = $validateCertificates['certificate'];
        $websiteSslCertificate->private_key = $validateCertificates['private_key'];
        $websiteSslCertificate->certificate_chain = $validateCertificates['certificate_chain'];
        $websiteSslCertificate->customer_id = $event->model->customer_id;
        $websiteSslCertificate->is_active = 1;
        $websiteSslCertificate->is_wildcard = 0;
        $websiteSslCertificate->is_auto_renew = 1;
        $websiteSslCertificate->provider = 'letsencrypt';
        $websiteSslCertificate->save();

        $findDomain->configureVirtualHost();

    }
}
