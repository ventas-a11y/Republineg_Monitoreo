<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ends a login after session.absolute_lifetime minutes even if the user stays
 * active; the idle limit alone (session.lifetime) never expires a busy session.
 */
class EnforceAbsoluteSessionTimeout
{
    public const string STARTED_AT_KEY = 'auth.session_started_at';

    private const string STARTED_FOR_KEY = 'auth.session_started_for';

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        $session = $request->session();

        if ($session->get(self::STARTED_FOR_KEY) !== $user->getAuthIdentifier()) {
            $session->put(self::STARTED_FOR_KEY, $user->getAuthIdentifier());
            $session->put(self::STARTED_AT_KEY, now()->getTimestamp());
        }

        $expiresAt = (int) $session->get(self::STARTED_AT_KEY) + config('session.absolute_lifetime') * 60;

        if (now()->getTimestamp() >= $expiresAt) {
            Auth::guard('web')->logout();
            $session->invalidate();
            $session->regenerateToken();

            return redirect()->route('login')->with('status', 'Tu sesión venció. Vuelve a iniciar sesión.');
        }

        return $next($request);
    }
}
