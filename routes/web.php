<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::resource('analyses', \App\Http\Controllers\AnalysisController::class);
    
    // Add this new route for saving analysis data
    Route::post('analyses/save', [\App\Http\Controllers\AnalysisController::class, 'saveData'])
        ->name('analyses.save');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
