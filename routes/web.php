<?php

use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestCaseController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// main page
Route::get('/dashboard', [ModuleController::class, 'index'])->middleware(['auth'])->name('dashboard');

// modules
Route::middleware(['auth'])->prefix('modules')->name('modules.')->group(function () {
    Route::get('/{moduleId?}', [ModuleController::class, 'getSubModules'])->name('options');
    Route::post('/', [ModuleController::class, 'store'])->name('store');
    Route::put('/{moduleId}', [ModuleController::class, 'update'])->name('update');
    Route::delete('/{moduleId}', [ModuleController::class, 'destroy'])->name('destroy');

    Route::post('/refresh', [ModuleController::class, 'refresh'])->name('refresh');
    Route::post('/drag', [ModuleController::class, 'syncDrag'])->name('syncDrag');
});


// test case
Route::get('/module/{moduleId}/testCase', [TestCaseController::class, 'index'])->middleware(['auth'])->name('modules.testCase');


// google auth
Route::get('/auth/google', [GoogleAuthController::class, 'authWithGoogle'])->name('auth.google');
Route::get('/auth/google/call-back', [GoogleAuthController::class, 'authWithGoogleCallback']);

// auth
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
