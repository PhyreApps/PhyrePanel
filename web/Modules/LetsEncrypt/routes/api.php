<?php

use App\ApiClient;
use App\Jobs\ApacheBuild;
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

    $findHostingSubscription = \App\Models\HostingSubscription::where('id', $findDomain->hosting_subscription_id)->first();
    if (! $findHostingSubscription) {
        return response()->json(['error' => 'Domain not hosted'], 400);
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

    $exec = shell_exec($certbotHttpSecureCommand);

    $validateCertificates = [];

    if (! file_exists($sslCertificateFilePath)
        || ! file_exists($sslCertificateKeyFilePath)
        || ! file_exists($sslCertificateChainFilePath)) {
        // Cant get all certificates
        return response()->json(['error' => 'Cant get all certificates'], 400);
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

    $findDomain->configureVirtualHost(true);

    ApacheBuild::dispatchSync();

    return [
        'success' => 'Domain secured successfully'
    ];

});
