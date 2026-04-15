<?php

use App\Modules\Central\Presentation\Controllers\TenantController;

Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

Route::prefix('central')->group(function () {
    Route::post('/tenants', [TenantController::class, 'store']);
});