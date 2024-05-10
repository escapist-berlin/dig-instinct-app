<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReleaseController;
use App\Http\Controllers\UserListController;
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

Route::get('/dashboard', [ReleaseController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/releases/{release}/tracks/{track}/like', [ReleaseController::class, 'likeTrack'])
        ->name('releases.like-track');
    Route::delete('/releases/{release}/tracks/{track}/unlike', [ReleaseController::class, 'unlikeTrack'])
        ->name('releases.unlike-track');

    Route::post('releases/{release}/update-list', [ReleaseController::class, 'updateList'])
        ->name('releases.update-list');
});

Route::resource('releases', ReleaseController::class)
    ->only(['index', 'store', 'show'])
    ->middleware(['auth', 'verified']);

Route::resource('user-lists', UserListController::class)
    ->only(['index', 'store', 'show', 'update', 'destroy'])
    ->middleware(['auth', 'verified']);

require __DIR__.'/auth.php';
