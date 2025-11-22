<?php

use App\Http\Controllers\UserSubmissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/submit-idea', [UserSubmissionController::class, 'store']);


use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:api')->get('/user', [AuthController::class, 'user']);

Route::get('admin/ideas', [AdminDashboardController::class, 'index']);
Route::get('admin/ideas/{id}', [AdminDashboardController::class, 'show']);

Route::middleware(['auth:api'])->prefix('admin')->group(function () {
    // Route::get('/ideas', [AdminDashboardController::class, 'index']);
    Route::post('/ideas', [AdminDashboardController::class, 'store']);
    // Route::get('/ideas/{id}', [AdminDashboardController::class, 'show']);
    Route::post('/ideas/{id}/approve', [AdminDashboardController::class, 'approve']);
    Route::post('/ideas/{id}/reject', [AdminDashboardController::class, 'reject']);
    Route::put('/ideas/{id}', [AdminDashboardController::class, 'update']);
    Route::delete('/ideas/{id}', [AdminDashboardController::class, 'destroy']);
});
