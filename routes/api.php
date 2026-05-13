<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\AuthController as UserAuthController;
use App\Http\Controllers\Provider\AuthController as ProviderAuthController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ProviderManagementController;
Route::prefix('user/auth')->group(function () {
    Route::middleware('throttle:narrow')->group(function () {
        Route::post('/verify-phone', [UserAuthController::class, 'verifyPhone']);
        Route::post('/login', [UserAuthController::class, 'login']);
        Route::post('/verify-phone-for-reset-password', [UserAuthController::class, 'verifyPhoneForResetPassword']);
        Route::post('/send-otp-code', [UserAuthController::class, 'sendOtpCode']);
        Route::post('/refresh-tokens', [UserAuthController::class, 'refreshTokens']);
        Route::post('/reset-password', [UserAuthController::class, 'resetPassword']);
    });
    Route::post('/register', [UserAuthController::class, 'register']);
    Route::middleware(['auth:user'])->group(function () {
        Route::get('/me', [UserAuthController::class, 'me']);
        Route::post('/logout', [UserAuthController::class, 'logout']);
    });
});

Route::prefix('provider/auth')->group(function () {
    Route::middleware('throttle:narrow')->group(function () {
        Route::post('/login', [ProviderAuthController::class, 'login']);
        Route::post('/send-otp-code', [ProviderAuthController::class, 'sendOtpCode']);
        Route::post('/refresh-tokens', [ProviderAuthController::class, 'refreshTokens']);
        Route::post('/verify-phone-for-reset-password', [ProviderAuthController::class, 'verifyPhoneForResetPassword']);
        Route::post('/reset-password', [ProviderAuthController::class, 'resetPassword']);
    });
    Route::middleware(['auth:provider'])->group(function () {
        Route::get('/me', [ProviderAuthController::class, 'me']);
        Route::post('/logout', [ProviderAuthController::class, 'logout']);
    });
});

Route::prefix('admin')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::middleware('throttle:narrow')->group(function () {
            Route::post('/login', [AdminAuthController::class, 'login']);
            Route::post('/verify-login', [AdminAuthController::class, 'verifyLogin']);
            Route::post('/refresh-tokens', [AdminAuthController::class, 'refreshTokens']);
        });
        Route::middleware(['auth:admin'])->group(function () {
            Route::get('/me', [AdminAuthController::class, 'me']);
            Route::post('/change-password', [AdminAuthController::class, 'changePassword']);
            Route::post('/logout', [AdminAuthController::class, 'logout']);
        });
    });
    Route::prefix('provider')->middleware(['auth:admin'])->group(function () {
        Route::get('/all-providers', [ProviderManagementController::class, 'getAllProviders']);
        Route::post('/create-provider', [ProviderManagementController::class, 'createProvider']);
        Route::get('/providers/{id}', [ProviderManagementController::class, 'getProviderById']);
        Route::put('/providers/{id}', [ProviderManagementController::class, 'updateProvider']);
        Route::delete('/providers/{id}', [ProviderManagementController::class, 'deleteProvider']);
        Route::post('/providers/{id}/activate', [ProviderManagementController::class, 'activateProvider']);
        Route::post('/providers/{id}/deactivate', [ProviderManagementController::class, 'deactivateProvider']);
        Route::get('/providers/search', [ProviderManagementController::class, 'searchProviders']);
    });
});
//Z+x+c+v+b+n+m-1