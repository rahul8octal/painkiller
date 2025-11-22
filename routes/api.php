<?php

use App\Http\Controllers\UserSubmissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/submit-idea', [UserSubmissionController::class, 'store']);


Route::prefix('admin')->group(function () {
    Route::get('/ideas', [AdminDashboardController::class, 'index']);
    Route::get('/ideas/{id}', [AdminDashboardController::class, 'show']);
    Route::post('/ideas/{id}/approve', [AdminDashboardController::class, 'approve']);
    Route::post('/ideas/{id}/reject', [AdminDashboardController::class, 'reject']);
    Route::put('/ideas/{id}', [AdminDashboardController::class, 'update']);
});
