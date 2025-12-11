<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ApplyLoanController;
use App\Http\Controllers\NotificationController;

Route::get('/test', function () {
    return ['status' => 'API working'];
});


Route::post('/register', [RegisteredUserController::class, 'store']);
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

Route::post('/apply-loan/store', [ApplyLoanController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::post('/fcm/save-token', [NotificationController::class, 'saveToken']);
    Route::post('/notify/all', [NotificationController::class, 'notifyAll']);
    Route::post('/loan/submit-form', [LoanController::class, 'store']);
});
