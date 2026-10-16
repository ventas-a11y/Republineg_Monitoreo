<?php

use App\Http\Controllers\Auth\TemporaryPasswordController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware('auth')->group(function () {
    Route::get('password/temporary', [TemporaryPasswordController::class, 'edit'])->name('temporary-password.edit');
    Route::put('password/temporary', [TemporaryPasswordController::class, 'update'])->name('temporary-password.update');
});

/*
 * The panel: temporary password replaced, email verified and a second factor
 * configured, in that order (docs/SEGURIDAD.md, Q52).
 */
Route::middleware(['auth', 'password.changed', 'verified', 'second-factor'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    require __DIR__.'/monitoring.php';
    require __DIR__.'/admin.php';
});

require __DIR__.'/settings.php';
