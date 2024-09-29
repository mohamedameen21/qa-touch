<?php

use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
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

Route::get('/dashboard', [ModuleController::class,'index'])->middleware(['auth'])->name('dashboard');

Route::get('/modules/{moduleId?}', [ModuleController::class,'getDirectChildren'])->middleware(['auth'])->name('modules.options');
Route::post('/modules/{moduleId?}', [ModuleController::class,'store'])->middleware(['auth'])->name('modules.store');

Route::get('/auth/google', [GoogleAuthController::class, 'authWithGoogle'])->name('auth.google');
Route::get('/auth/google/call-back', [GoogleAuthController::class, 'authWithGoogleCallback']);


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
