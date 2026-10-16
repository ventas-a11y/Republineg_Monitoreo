<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\TemporaryPasswordUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

/**
 * First login after an invitation: the temporary password must be replaced
 * before anything else in the panel is reachable.
 */
class TemporaryPasswordController extends Controller
{
    /**
     * Show the form to replace the temporary password.
     */
    public function edit(Request $request): Response|RedirectResponse
    {
        if (! $request->user()?->password_change_required) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('auth/change-temporary-password', [
            'passwordRules' => Password::defaults()->toPasswordRulesString(),
        ]);
    }

    /**
     * Replace the temporary password and continue to second-factor setup.
     */
    public function update(TemporaryPasswordUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->forceFill([
            'password' => $request->validated('password'),
            'password_change_required' => false,
            'temporary_password_expires_at' => null,
            // Reading the invitation email proves ownership of the address.
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        // The user just typed their password; skip a redundant confirmation prompt.
        $request->session()->passwordConfirmed();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Contraseña actualizada. Ahora configura tu segundo factor.']);

        return redirect()->route('security.edit');
    }
}
