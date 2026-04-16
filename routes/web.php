<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\PageController;

// ─── Landing page ─────────────────────────────────────────────────────────────
Route::get('/', function () { return view('welcome'); })->name('home');

// ─── Language switch ──────────────────────────────────────────────────────────
Route::get('/language/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back()->withInput();
})->name('language');

// ─── Auth ─────────────────────────────────────────────────────────────────────
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');
Route::post('/switch-tenant', [AuthController::class, 'switchTenantWeb'])->name('switch-tenant');

// ─── Protected pages ─────────────────────────────────────────────────────────
Route::middleware('web.auth')->group(function () {
    Route::get('/dashboard',     [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/students',      [PageController::class, 'students'])->name('students');
    Route::get('/teachers',      [PageController::class, 'teachers'])->name('teachers');
    Route::get('/courses',       [PageController::class, 'courses'])->name('courses');
    Route::get('/classes',       [PageController::class, 'classes'])->name('classes');
    Route::get('/enrollments',   [PageController::class, 'enrollments'])->name('enrollments');
    Route::get('/assignments',   [PageController::class, 'assignments'])->name('assignments');
    Route::get('/announcements', [PageController::class, 'announcements'])->name('announcements');
    Route::get('/events',        [PageController::class, 'events'])->name('events');
});
