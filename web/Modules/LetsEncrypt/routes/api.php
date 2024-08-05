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
    if (!$findDomain) {
        return response()->json(['error' => 'Domain not found'], 404);
    }

    $findSSL = DomainSslCertificate::where('domain', $findDomain->domain)->first();
    if ($findSSL) {
        return response()->json(['error' => 'Domain already secured'], 400);
    }

    $findHostingSubscription = \App\Models\HostingSubscription::where('id', $findDomain->hosting_subscription_id)->first();
    if (!$findHostingSubscription) {
        return response()->json(['error' => 'Domain not hosted'], 400);
    }

    try {
        $secureDomain = new \Modules\LetsEncrypt\Jobs\LetsEncryptSecureDomain($findDomain->id);
        $secureDomain->handle();

        ApacheBuild::dispatchSync();

        return [
            'success' => 'Domain secured successfully'
        ];
    } catch (\Exception $e) {
        return response()->json(['error' => 'Can\'t secure domain'], 500);
    }

});
