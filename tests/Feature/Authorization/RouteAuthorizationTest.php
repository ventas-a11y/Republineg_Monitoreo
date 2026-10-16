<?php

use App\Models\Area;
use App\Models\User;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;

/**
 * @return array<string, array{0: string, 1: array<string, string>}>
 */
function authorizationMatrix(): array
{
    return require dirname(__DIR__, 2).'/Fixtures/authorization-matrix.php';
}

/**
 * Routes that intentionally need no login.
 */
const PUBLIC_ROUTES = ['home'];

dataset('authorization matrix', function () {
    foreach (authorizationMatrix() as $routeName => [$method, $outcomes]) {
        foreach ($outcomes as $actor => $outcome) {
            yield "{$actor} {$method} {$routeName} -> {$outcome}" => [$routeName, $method, $actor, $outcome];
        }
    }
});

test('each route answers each actor as the matrix says', function (string $routeName, string $method, string $actor, string $outcome) {
    Notification::fake();

    $area = Area::factory()->create();
    $empleado = User::factory()->empleado($area)->create();

    $signedIn = match ($actor) {
        'guest' => null,
        'empleado' => User::factory()->empleado($area)->withTwoFactor()->create(),
        'revisor' => User::factory()->revisor($area)->withTwoFactor()->create(),
        'administrador' => User::factory()->administrador()->withTwoFactor()->create(),
    };

    if ($signedIn !== null) {
        $this->actingAs($signedIn);
    }

    $parameters = collect(Route::getRoutes()->getByName($routeName)->parameterNames())
        ->mapWithKeys(fn (string $name): array => [$name => match ($name) {
            'user' => $empleado,
            'area' => $area,
        }])
        ->all();

    $response = $this->call($method, route($routeName, $parameters));

    match ($outcome) {
        'login' => $response->assertRedirect(route('login')),
        'not_found' => $response->assertNotFound(),
        'allowed' => expect($response->getStatusCode())->not->toBeIn([401, 403, 404])
            ->and($response->headers->get('Location'))->not->toBe(route('login')),
    };
})->with('authorization matrix');

test('every application route is in the authorization matrix and requires a login', function () {
    $applicationRoutes = collect(Route::getRoutes()->getRoutes())
        ->filter(fn (RoutingRoute $route): bool => $route->getName() !== null
            && (str_starts_with($route->getActionName(), 'App\\') || str_starts_with($route->getActionName(), 'Inertia\\')))
        ->reject(fn (RoutingRoute $route): bool => in_array($route->getName(), PUBLIC_ROUTES, true));

    $missingFromMatrix = $applicationRoutes
        ->map(fn (RoutingRoute $route): string => (string) $route->getName())
        ->diff(array_keys(authorizationMatrix()))
        ->values()
        ->all();

    $withoutLogin = $applicationRoutes
        ->reject(fn (RoutingRoute $route): bool => in_array('auth', $route->gatherMiddleware(), true))
        ->map(fn (RoutingRoute $route): string => (string) $route->getName())
        ->values()
        ->all();

    expect($missingFromMatrix)->toBe([], 'Routes missing from tests/Fixtures/authorization-matrix.php')
        ->and($withoutLogin)->toBe([], 'Application routes without the auth middleware');
});
