<?php

namespace App\Http\Middleware;

use App\Models\Area;
use App\Models\MonitoringPolicy;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user,
                'can' => [
                    'viewMonitoring' => $user?->can('viewMonitoring') ?? false,
                    'manageAccounts' => $user?->can('viewAny', User::class) ?? false,
                    'manageAreas' => $user?->can('viewAny', Area::class) ?? false,
                    'manageMonitoringPolicy' => $user?->can('viewAny', MonitoringPolicy::class) ?? false,
                ],
            ],
            'sessionTimeout' => $user === null ? null : [
                'idleMinutes' => (int) config('session.lifetime'),
                'absoluteExpiresAt' => (int) $request->session()->get(EnforceAbsoluteSessionTimeout::STARTED_AT_KEY)
                    + (int) config('session.absolute_lifetime') * 60,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
