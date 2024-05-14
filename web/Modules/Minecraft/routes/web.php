<?php

use Illuminate\Support\Facades\Route;
use Modules\Minecraft\App\Http\Controllers\MinecraftController;

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
    Route::resource('minecraft', MinecraftController::class)->names('minecraft');
});
