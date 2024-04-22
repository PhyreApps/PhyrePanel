<?php

use Illuminate\Support\Facades\Route;
use phpseclib3\Net\SSH2;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


if (!file_exists(storage_path('installed'))) {
    Route::get('/', \App\Livewire\Installer::class);
} else {
    Route::get('/', function () {
        return redirect('/admin');
    });
}

Route::get('/installer', \App\Livewire\Installer::class);


Route::get('vurti', function () {

    $findDomain = \App\Models\Domain::where('id', 41)->first();

    $findDomain->configureVirtualHost();

    dd(3);

});
