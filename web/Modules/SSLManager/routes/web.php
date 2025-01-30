<?php

use Illuminate\Support\Facades\Route;
use Modules\SSLManager\App\Http\Controllers\SSLManagerController;

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
    Route::resource('sslmanager', SSLManagerController::class)->names('sslmanager');
});
