<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps a user who still has the temporary password from the invitation on
 * the screen where it must be replaced.
 */
class EnsurePasswordIsChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->password_change_required) {
            return redirect()->route('temporary-password.edit');
        }

        return $next($request);
    }
}
