<?php

/*
|--------------------------------------------------------------------------
| Authorization matrix (docs/SEGURIDAD.md, Authorization_Testing_Automation)
|--------------------------------------------------------------------------
|
| The single source of truth for who may reach each application route. Every
| route defined by the application must appear here: RouteAuthorizationTest
| fails when a new route is missing. Actors are signed in with a verified
| email and a second factor; the guest is not signed in.
|
| Outcomes:
|   login     -> redirected to the login page
|   not_found -> 404 (a denied resource never confirms it exists)
|   allowed   -> passes authentication and authorization (a validation error
|                for the empty request still counts as allowed)
|
| Route parameters are filled with an Área ({area}) and an Empleado ({user}).
|
*/

$everyone = ['guest' => 'login', 'empleado' => 'allowed', 'revisor' => 'allowed', 'administrador' => 'allowed'];
$monitoring = ['guest' => 'login', 'empleado' => 'not_found', 'revisor' => 'allowed', 'administrador' => 'allowed'];
$administradorOnly = ['guest' => 'login', 'empleado' => 'not_found', 'revisor' => 'not_found', 'administrador' => 'allowed'];

return [
    'dashboard' => ['GET', $everyone],

    // Monitoring screens: Revisores and Administradores only.
    'devices.index' => ['GET', $monitoring],
    'activity.timeline' => ['GET', $monitoring],
    'activity.apps' => ['GET', $monitoring],
    'captures.index' => ['GET', $monitoring],
    'alerts.index' => ['GET', $monitoring],
    'reports.index' => ['GET', $monitoring],

    // Own account settings.
    'profile.edit' => ['GET', $everyone],
    'profile.update' => ['PATCH', $everyone],
    'security.edit' => ['GET', $everyone],
    'user-password.update' => ['PUT', $everyone],
    'appearance.edit' => ['GET', $everyone],

    // Only meaningful while a temporary password is pending; otherwise the
    // form redirects to the dashboard and the update answers 404.
    'temporary-password.edit' => ['GET', $everyone],
    'temporary-password.update' => ['PUT', ['guest' => 'login', 'empleado' => 'not_found', 'revisor' => 'not_found', 'administrador' => 'not_found']],

    // Administración.
    'admin.users.index' => ['GET', $administradorOnly],
    'admin.users.create' => ['GET', $administradorOnly],
    'admin.users.store' => ['POST', $administradorOnly],
    'admin.users.edit' => ['GET', $administradorOnly],
    'admin.users.update' => ['PUT', $administradorOnly],
    'admin.users.invitation' => ['POST', $administradorOnly],
    'admin.areas.index' => ['GET', $administradorOnly],
    'admin.areas.store' => ['POST', $administradorOnly],
    'admin.areas.edit' => ['GET', $administradorOnly],
    'admin.areas.update' => ['PUT', $administradorOnly],
];
