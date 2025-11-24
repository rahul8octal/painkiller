<?php

use App\Http\Controllers\UserSubmissionController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use LemonSqueezy\Laravel\Http\Controllers\WebhookController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/webhooks/lemon-squeezy', WebhookController::class);
Route::get('/subscription/products', [CheckoutController::class, 'products']);

Route::get('/admin/ideas', [AdminDashboardController::class, 'index']);
Route::get('admin/ideas/{id}', [AdminDashboardController::class, 'show']);

Route::middleware(['auth:api'])->group(function () {
    Route::post('/checkout/{variantId}', [CheckoutController::class, 'create']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/submit-idea', [UserSubmissionController::class, 'store']);

    // Admin Routes (Protected by AdminMiddleware)
    Route::middleware(['admin'])->prefix('admin')->group(function () {
        Route::post('/ideas', [AdminDashboardController::class, 'store']);
        Route::post('/ideas/{id}/approve', [AdminDashboardController::class, 'approve']);
        Route::post('/ideas/{id}/reject', [AdminDashboardController::class, 'reject']);
        Route::put('/ideas/{id}', [AdminDashboardController::class, 'update']);
        Route::delete('/ideas/{id}', [AdminDashboardController::class, 'destroy']);
    });
});
