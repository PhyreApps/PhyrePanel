<?php

use Illuminate\Support\Facades\Route;
use Modules\Customer\App\Http\Controllers\CustomerController;

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

Route::get('/customer/phpMyAdmin/login/{id}', [\Modules\Customer\App\Http\Controllers\PHPMyAdminController::class, 'login'])
    ->name('customer.phpmyadmin.login');
