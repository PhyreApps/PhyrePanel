<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('health', [\App\Http\Controllers\Api\HealthController::class, 'index'])->name('api.health');

Route::middleware(\App\Http\Middleware\ApiKeyMiddleware::class)->group(function() {

    // Customers
    Route::get('customers', [\App\Http\Controllers\Api\CustomersController::class, 'index'])->name('api.customers.index');
    Route::post('customers', [\App\Http\Controllers\Api\CustomersController::class, 'store'])->name('api.customers.store');
    Route::get('customers/{id}', [\App\Http\Controllers\Api\CustomersController::class, 'show'])->name('api.customers.show');
    Route::put('customers/{id}', [\App\Http\Controllers\Api\CustomersController::class, 'update'])->name('api.customers.update');
    Route::delete('customers/{id}', [\App\Http\Controllers\Api\CustomersController::class, 'destroy'])->name('api.customers.destroy');
    Route::get('customers/{id}/hosting-subscriptions', [\App\Http\Controllers\Api\CustomersController::class, 'getHostingSubscriptionsByCustomerId'])
        ->name('api.customers.hosting-subscriptions');

    Route::get('/customers/{id}/generate-login-token', [\App\Http\Controllers\Api\CustomersController::class, 'generateLoginToken'])
        ->name('api.customers.generate-login-token');

    // Hosting subscriptions
    Route::get('hosting-subscriptions', [\App\Http\Controllers\Api\HostingSubscriptionsController::class, 'index'])->name('api.hosting-subscriptions.index');
    Route::post('hosting-subscriptions', [\App\Http\Controllers\Api\HostingSubscriptionsController::class, 'store'])->name('api.hosting-subscriptions.store');
    Route::put('hosting-subscriptions/{id}', [\App\Http\Controllers\Api\HostingSubscriptionsController::class, 'update'])->name('api.hosting-subscriptions.update');
    Route::delete('hosting-subscriptions/{id}', [\App\Http\Controllers\Api\HostingSubscriptionsController::class, 'destroy'])->name('api.hosting-subscriptions.destroy');
    Route::post('hosting-subscriptions/{id}/suspend', [\App\Http\Controllers\Api\HostingSubscriptionsController::class, 'suspend'])->name('api.hosting-subscriptions.suspend');
    Route::post('hosting-subscriptions/{id}/unsuspend', [\App\Http\Controllers\Api\HostingSubscriptionsController::class, 'unsuspend'])->name('api.hosting-subscriptions.unsuspend');

    // Domains
    Route::get('domains', [\App\Http\Controllers\Api\DomainsController::class, 'index'])->name('api.domains.index');
    Route::post('domains', [\App\Http\Controllers\Api\DomainsController::class, 'store'])->name('api.domains.store');
    Route::put('domains/{id}', [\App\Http\Controllers\Api\DomainsController::class, 'update'])->name('api.domains.update');
    Route::delete('domains/{id}', [\App\Http\Controllers\Api\DomainsController::class, 'destroy'])->name('api.domains.destroy');

    Route::get('hosting-plans', [\App\Http\Controllers\Api\HostingPlansController::class, 'index'])->name('api.hosting-plans.index');
    Route::post('hosting-plans', [\App\Http\Controllers\Api\HostingPlansController::class, 'store'])->name('api.hosting-plans.store');

});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
