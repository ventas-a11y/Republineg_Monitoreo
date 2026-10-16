<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\FailedPasswordResetLinkRequestResponse;

/**
 * Answers a failed reset-link request (unknown email, throttled) exactly like
 * a successful one, so the form never reveals which emails have an account
 * (docs/SEGURIDAD.md, Authentication: account enumeration).
 */
class IndistinguishablePasswordResetLinkResponse implements FailedPasswordResetLinkRequestResponse
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  Request  $request
     */
    public function toResponse($request): JsonResponse|RedirectResponse
    {
        return $request->wantsJson()
            ? new JsonResponse(['message' => trans('passwords.sent')], 200)
            : back()->with('status', trans('passwords.sent'));
    }
}
