<?php

namespace App\Http\Controllers\Consent;

use App\Actions\Consent\AcceptMonitoringPolicy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Consent\AcceptMonitoringPolicyRequest;
use App\Models\MonitoringPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The Política de monitoreo as every account sees it, and its acceptance by
 * an Empleado.
 */
class MonitoringPolicyController extends Controller
{
    /**
     * Show the version in force and whether the user already accepted it.
     */
    public function show(Request $request): Response
    {
        $user = $request->user();
        $current = MonitoringPolicy::current();

        $acceptance = $current === null
            ? null
            : $user->policyAcceptances()->where('monitoring_policy_id', $current->id)->first();

        return Inertia::render('consent/monitoring-policy', [
            'policy' => $current === null ? null : [
                'id' => $current->id,
                'version' => $current->version,
                'title' => $current->title,
                'published_at' => $current->published_at->toIso8601String(),
                // Admin-written Markdown converted server-side with raw HTML stripped.
                'body_html' => $current->bodyHtml(),
            ],
            'mustAccept' => $user->mustAcceptMonitoringPolicy(),
            'acceptedAt' => $acceptance?->accepted_at->toIso8601String(),
        ]);
    }

    /**
     * Record the Empleado's acceptance of the version in force.
     */
    public function accept(AcceptMonitoringPolicyRequest $request, AcceptMonitoringPolicy $acceptMonitoringPolicy): RedirectResponse
    {
        $acceptMonitoringPolicy->handle(
            $request->user(),
            MonitoringPolicy::query()->whereKey($request->string('monitoring_policy_id')->toString())->firstOrFail(),
            $request->ip(),
            $request->userAgent(),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Gracias. Tu aceptación quedó registrada.']);

        return redirect()->route('dashboard');
    }
}
