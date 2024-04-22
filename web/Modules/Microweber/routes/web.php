<?php

use Illuminate\Support\Facades\Route;
use Modules\Microweber\App\Http\Controllers\MicroweberController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([], function () {
    Route::resource('microweber', MicroweberController::class)->names('microweber');
});

Route::get('waaw', function () {

    $whiteLabelSettings = [];
    $whiteLabelSettings['whmcs_url'] = 'qko';

    $whitelabel = new \MicroweberPackages\SharedServerScripts\MicroweberWhitelabelSettingsUpdater();
    $whitelabel->setPath(config('microweber.sharedPaths.app'));
    $apply = $whitelabel->apply($whiteLabelSettings);

    dd($apply);


});
