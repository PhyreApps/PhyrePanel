<?php

use App\ApiClient;
use App\Models\DomainSslCertificate;
use App\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/

//Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
//    Route::get('letsencrypt', fn (Request $request) => $request->user())->name('letsencrypt');
//});

Route::post('letsencrypt/secure', function () {

    $domain = request('domain', null);
    $email = request('email', null);

    $findDomain = \App\Models\Domain::where('domain', $domain)->first();
    if (! $findDomain) {
        return response()->json(['error' => 'Domain not found'], 404);
    }

    $findSSL = DomainSslCertificate::where('domain', $findDomain->domain)->first();
    if ($findSSL) {
        return response()->json(['error' => 'Domain already secured'], 400);
    }

    $generalSettings = Settings::general();

    $acmeConfigYaml = view('letsencrypt::actions.acme-config-yaml', [
        'domain' => $findDomain->domain,
        'domainRoot' => $findDomain->domain_root,
        'domainPublic' => $findDomain->domain_public,
        'email' => $generalSettings['master_email'],
        'country' => $generalSettings['master_country'],
        'locality' => $generalSettings['master_locality'],
        'organization' => $generalSettings['organization_name'],
    ])->render();

    file_put_contents($findDomain->domain_root.'/acme-config.yaml', $acmeConfigYaml);

    $amePHPPharFile = base_path().'/Modules/LetsEncrypt/Actions/acmephp.phar';

    $phyrePHP = ApiClient::getPhyrePHP();

    $command = $phyrePHP.' '.$amePHPPharFile.' run '.$findDomain->domain_root.'/acme-config.yaml';
    $execSSL = shell_exec($command);

    $validateCertificates = [];
    $sslCertificateFilePath = '/root/.acmephp/master/certs/'.$findDomain->domain.'/public/cert.pem';
    $sslCertificateKeyFilePath = '/root/.acmephp/master/certs/'.$findDomain->domain.'/private/key.private.pem';
    $sslCertificateChainFilePath = '/root/.acmephp/master/certs/'.$findDomain->domain.'/public/fullchain.pem';

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

    $findDomain->configureVirtualHost();

    return [
        'success' => 'Domain secured successfully'
    ];

});
