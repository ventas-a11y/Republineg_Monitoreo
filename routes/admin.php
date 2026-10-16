<?php

use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\MonitoringPolicyController;
use App\Http\Controllers\Admin\UserController;
use App\Models\Area;
use App\Models\MonitoringPolicy;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
 * Administración: accounts and Áreas. Loaded inside the panel group in
 * web.php; each route is authorized by its Policy (404 for non-admins).
 */
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('usuarios', [UserController::class, 'index'])
        ->middleware('can:viewAny,'.User::class)->name('users.index');
    Route::get('usuarios/crear', [UserController::class, 'create'])
        ->middleware('can:create,'.User::class)->name('users.create');
    Route::post('usuarios', [UserController::class, 'store'])
        ->middleware('can:create,'.User::class)->name('users.store');
    Route::get('usuarios/{user}/editar', [UserController::class, 'edit'])
        ->middleware('can:update,user')->name('users.edit');
    Route::put('usuarios/{user}', [UserController::class, 'update'])
        ->middleware('can:update,user')->name('users.update');
    Route::post('usuarios/{user}/invitacion', [UserController::class, 'resendInvitation'])
        ->middleware('can:resendInvitation,user')->name('users.invitation');

    Route::get('areas', [AreaController::class, 'index'])
        ->middleware('can:viewAny,'.Area::class)->name('areas.index');
    Route::post('areas', [AreaController::class, 'store'])
        ->middleware('can:create,'.Area::class)->name('areas.store');
    Route::get('areas/{area}/editar', [AreaController::class, 'edit'])
        ->middleware('can:update,area')->name('areas.edit');
    Route::put('areas/{area}', [AreaController::class, 'update'])
        ->middleware('can:update,area')->name('areas.update');

    Route::get('politica', [MonitoringPolicyController::class, 'index'])
        ->middleware('can:viewAny,'.MonitoringPolicy::class)->name('monitoring-policy.index');
    Route::post('politica', [MonitoringPolicyController::class, 'store'])
        ->middleware('can:create,'.MonitoringPolicy::class)->name('monitoring-policy.store');
})->whereUlid(['user', 'area']);
