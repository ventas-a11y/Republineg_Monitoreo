<?php

namespace App\Http\Middleware;

use App\Models\MonitoringPolicy;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * An Empleado reads and accepts the current Política de monitoreo before using
 * the panel. Revisores and Administradores are not monitored, so they pass.
 */
class EnsureMonitoringPolicyAccepted
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->mustAcceptMonitoringPolicy()) {
            return $next($request);
        }

        $current = MonitoringPolicy::current();

        if ($current !== null && ! $user->policyAcceptances()->where('monitoring_policy_id', $current->id)->exists()) {
            return redirect()->route('monitoring-policy.show');
        }

        return $next($request);
    }
}
