<?php

use App\Http\Controllers\Auth\TemporaryPasswordController;
use App\Http\Controllers\Consent\MonitoringPolicyController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware('auth')->group(function () {
    Route::get('password/temporary', [TemporaryPasswordController::class, 'edit'])->name('temporary-password.edit');
    Route::put('password/temporary', [TemporaryPasswordController::class, 'update'])->name('temporary-password.update');
});

// The Política de monitoreo: readable by every account, accepted by Empleados.
Route::middleware(['auth', 'password.changed'])->group(function () {
    Route::get('politica', [MonitoringPolicyController::class, 'show'])->name('monitoring-policy.show');
    Route::post('politica/aceptar', [MonitoringPolicyController::class, 'accept'])->name('monitoring-policy.accept');
});

/*
 * The panel: temporary password replaced, current Política de monitoreo
 * accepted (Empleados), email verified and a second factor configured, in
 * that order (docs/SEGURIDAD.md, Q43 and Q52).
 */
Route::middleware(['auth', 'password.changed', 'consent', 'verified', 'second-factor'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');

    require __DIR__.'/monitoring.php';
    require __DIR__.'/admin.php';
});

require __DIR__.'/settings.php';
