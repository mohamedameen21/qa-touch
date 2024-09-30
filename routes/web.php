<?php

use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestCaseController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('/dashboard');
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


Route::middleware(['auth'])->group(function() {
    // modules
   Route::prefix('modules')->name('modules.')->group(function() {
       Route::get('/{moduleId?}', [ModuleController::class, 'getSubModules'])->name('options');
       Route::post('/', [ModuleController::class, 'store'])->name('store');
       Route::put('/{moduleId}', [ModuleController::class, 'update'])->name('update');
       Route::delete('/{moduleId}', [ModuleController::class, 'destroy'])->name('destroy');

       Route::post('/refresh', [ModuleController::class, 'refresh'])->name('refresh');
       Route::post('/drag', [ModuleController::class, 'syncDrag'])->name('syncDrag');
   });

   // test case
    Route::prefix('module/{moduleId}')->group(function() {
        Route::get('/testcase', [TestCaseController::class, 'index'])->name('testCase.index');
        Route::post('/testcase', [TestCaseController::class, 'store'])->name('testCase.store');
        Route::put('/testcase/{testCaseId}', [TestCaseController::class, 'update'])->name('testCase.update');
        Route::delete('/testcase/{testCaseId}', [TestCaseController::class, 'destroy'])->name('testCase.destroy');
    });
});


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
