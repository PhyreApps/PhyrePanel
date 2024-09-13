<?php

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

//
//Route::middleware(['auth:sanctum'])->prefix('v1')->name('api.')->group(function () {
//    Route::get('customer', fn (Request $request) => $request->user())->name('customer');
//});

Route::get('/customer/phpMyAdmin/validate-token', [\Modules\Customer\App\Http\Controllers\PHPMyAdminController::class, 'validateToken'])
    ->name('customer.phpmyadmin.validate-token');
