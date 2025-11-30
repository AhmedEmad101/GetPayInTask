<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductControler;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentWebhookController;
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', [AuthController::class, 'login_execption'])->name('login');
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('holds')->group(function () {
         Route::post('/', [HoldController::class, 'store']);
    });
    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'get_user_orders']);
        Route::post('/', [OrderController::class, 'generate_order']);
            });
    Route::prefix('payments')->group(function () {
        Route::post('/webhook', [PaymentWebhookController::class, 'handle']);
        });

    Route::post('/logout', [AuthController::class, 'logout']);
});
 Route::prefix('products')->group(function () {
        Route::get('/{product}', [ProductControler::class, 'get_product'])->name('products.show');;
    });