<?php

use App\Modules\Central\Presentation\Controllers\AuthController;
use App\Modules\Central\Presentation\Controllers\TenantController;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('central')->group(function () {
        Route::post('/tenants', [TenantController::class, 'store']);
        Route::post('/current-tenant', [AuthController::class, 'setCurrentTenant']);
    });
});
