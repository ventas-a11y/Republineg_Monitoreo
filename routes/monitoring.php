<?php

use Illuminate\Support\Facades\Route;

/*
 * Monitoring screens for Revisores and Administradores (404 for an Empleado).
 * Loaded inside the panel group in web.php. Until each feature ticket lands,
 * the screen shows an empty state that says what is coming.
 */
Route::middleware('can:viewMonitoring')->group(function () {
    Route::inertia('equipos', 'coming-soon', [
        'title' => 'Equipos',
        'path' => '/equipos',
        'description' => 'Cada Equipo con su estado, su última Captura y las acciones Vista en vivo y Capturas.',
        'ticket' => 'ticket 05',
    ])->name('devices.index');

    Route::inertia('actividad/linea-de-tiempo', 'coming-soon', [
        'title' => 'Línea de tiempo',
        'path' => '/actividad/linea-de-tiempo',
        'description' => 'Cómo se repartió la jornada de cada persona, en Bloques de 10 minutos.',
        'ticket' => 'ticket 08',
    ])->name('activity.timeline');

    Route::inertia('actividad/apps-y-sitios', 'coming-soon', [
        'title' => 'Apps y sitios',
        'path' => '/actividad/apps-y-sitios',
        'description' => 'Tiempo por aplicación y por Sitio, con la Clasificación productiva, neutral o no productiva de cada Área.',
        'ticket' => 'ticket 14',
    ])->name('activity.apps');

    Route::inertia('capturas', 'coming-soon', [
        'title' => 'Capturas',
        'path' => '/capturas',
        'description' => 'Galería de Capturas con filtros por persona y fecha. Abrir una Captura queda en la Auditoría.',
        'ticket' => 'ticket 07',
    ])->name('captures.index');

    Route::inertia('alertas', 'coming-soon', [
        'title' => 'Alertas',
        'path' => '/alertas',
        'description' => 'Eventos que coinciden con tus Reglas de alerta. Son avisos para revisar, no acusaciones.',
        'ticket' => 'ticket 13',
    ])->name('alerts.index');

    Route::inertia('reportes', 'coming-soon', [
        'title' => 'Reportes',
        'path' => '/reportes',
        'description' => 'Generador de Reportes en CSV y PDF, envíos programados por correo e historial de descargas.',
        'ticket' => 'ticket 15',
    ])->name('reports.index');
});
