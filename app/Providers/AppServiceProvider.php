<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureGates();
    }

    /**
     * Abilities that are not tied to a single model.
     */
    protected function configureGates(): void
    {
        // Monitoring screens (Equipos, Actividad, Capturas, Alertas, Reportes) are for
        // Revisores and Administradores; an Empleado gets a 404, not a hint they exist.
        Gate::define('viewMonitoring', fn (User $user): Response => in_array($user->role, [Role::Revisor, Role::Administrador], true)
            ? Response::allow()
            : Response::denyAsNotFound());
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        // OWASP Authentication: length over composition rules; every account also
        // has a mandatory second factor (docs/SEGURIDAD.md, Q52 and Q53).
        Password::defaults(function (): Password {
            $rule = Password::min(8)->max(128);

            return app()->isProduction() ? $rule->uncompromised() : $rule;
        });
    }
}
