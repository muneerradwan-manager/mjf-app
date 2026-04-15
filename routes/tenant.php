<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureActiveTenantAccess;
use App\Http\Middleware\InitializeTenancyByCurrentUser;
use App\Modules\Tenant\Presentation\Controllers\GradeController;
use App\Modules\Tenant\Presentation\Controllers\AssignmentController;
use App\Modules\Tenant\Presentation\Controllers\EnrollmentController;
use App\Modules\Tenant\Presentation\Controllers\CourseController;
use App\Modules\Tenant\Presentation\Controllers\SubmissionController;
use App\Modules\Tenant\Presentation\Controllers\TeacherController;
use App\Modules\Tenant\Presentation\Controllers\ClassroomController;
use App\Modules\Tenant\Presentation\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::prefix('api/tenant')->middleware([
    'api',
    'auth:sanctum',
    InitializeTenancyByCurrentUser::class,
    EnsureActiveTenantAccess::class,
])->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'tenant_id' => tenant('id'),
            'user_id' => request()->user()->id,
            'current_tenant_id' => request()->user()->current_tenant_id,
        ]);
    });

    Route::apiResource('students', StudentController::class);
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('courses', CourseController::class);
    Route::apiResource('classes', ClassroomController::class);
    Route::apiResource('enrollments', EnrollmentController::class);
    Route::apiResource('assignments', AssignmentController::class);
    Route::apiResource('submissions', SubmissionController::class);
    Route::apiResource('grades', GradeController::class);
});
