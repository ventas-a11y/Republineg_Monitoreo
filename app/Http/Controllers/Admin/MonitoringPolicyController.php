<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Consent\PublishMonitoringPolicy;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PublishMonitoringPolicyRequest;
use App\Models\MonitoringPolicy;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Publishing the Política de monitoreo and tracking who accepted it.
 * Authorization lives on the routes (MonitoringPolicyPolicy via `can`).
 */
class MonitoringPolicyController extends Controller
{
    /**
     * Versions, the current text, and Empleados still pending acceptance.
     */
    public function index(): Response
    {
        $current = MonitoringPolicy::current();

        $pending = User::query()
            ->where('role', Role::Empleado)
            ->when($current !== null, fn ($query) => $query->whereDoesntHave(
                'policyAcceptances',
                fn ($acceptances) => $acceptances->where('monitoring_policy_id', $current->id),
            ))
            ->with('area:id,name')
            ->orderBy('name')
            ->get()
            ->map(fn (User $user): array => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'area' => $user->area?->name,
            ]);

        $versions = MonitoringPolicy::query()
            ->with('publisher:id,name')
            ->withCount('acceptances')
            ->orderByDesc('version')
            ->get()
            ->map(fn (MonitoringPolicy $policy): array => [
                'id' => $policy->id,
                'version' => $policy->version,
                'title' => $policy->title,
                'published_at' => $policy->published_at->toIso8601String(),
                'publisher' => $policy->publisher?->name,
                'acceptances_count' => $policy->acceptances_count,
            ]);

        return Inertia::render('admin/monitoring-policy/index', [
            'current' => $current === null ? null : [
                'version' => $current->version,
                'title' => $current->title,
                'body' => $current->body,
                'body_html' => $current->bodyHtml(),
            ],
            'versions' => $versions,
            'pending' => $current === null ? [] : $pending,
            'empleadosCount' => User::query()->where('role', Role::Empleado)->count(),
        ]);
    }

    /**
     * Publish a new version; every Empleado must accept it again.
     */
    public function store(PublishMonitoringPolicyRequest $request, PublishMonitoringPolicy $publishMonitoringPolicy): RedirectResponse
    {
        $policy = $publishMonitoringPolicy->handle(
            $request->user(),
            $request->validated('title'),
            $request->validated('body'),
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => "Se publicó la versión {$policy->version}. Cada Empleado debe aceptarla de nuevo.",
        ]);

        return redirect()->route('admin.monitoring-policy.index');
    }
}
