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

Route::get('/login', function () {
    return redirect(route('filament.admin.auth.login'));
})->name('login');


if (!file_exists(storage_path('installed'))) {
    Route::get('/', \App\Livewire\Installer::class);
} else {
    Route::get('/', function () {
        return redirect('/admin');
    });
}

Route::get('/installer', \App\Livewire\Installer::class);

Route::get('backup/download', [\App\Http\Controllers\BackupDownloadController::class, 'download'])
    ->name('backup.download');

Route::get('/customers/{id}/login-with-token', [\App\Http\Controllers\Api\CustomersController::class, 'loginWithToken'])
    ->name('customers.login-with-token');
