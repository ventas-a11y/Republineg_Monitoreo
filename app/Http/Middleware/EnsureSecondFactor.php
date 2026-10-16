<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

/**
 * Every account needs a second factor (TOTP or passkey) before using the
 * panel (docs/SEGURIDAD.md, Q52). Until then it is sent to Security settings.
 */
class EnsureSecondFactor
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() !== null && ! $request->user()->hasSecondFactor()) {
            Inertia::flash('toast', [
                'type' => 'warning',
                'message' => 'Configura la verificación en dos pasos o una passkey para usar el panel.',
            ]);

            return redirect()->route('security.edit');
        }

        return $next($request);
    }
}
