<?php

namespace App\Actions\Accounts;

use App\Models\User;
use App\Notifications\AccountInvitation;
use Illuminate\Support\Str;

/**
 * Gives an account a fresh temporary password and emails it as an invitation.
 * The password must be replaced on first login and expires after VALID_HOURS.
 */
class IssueTemporaryPassword
{
    public const int VALID_HOURS = 72;

    private const int PASSWORD_LENGTH = 16;

    /**
     * Issue the temporary password and send the invitation.
     *
     * The notification is sent synchronously on purpose: queuing it would store
     * the plaintext password in the queue payload.
     *
     * @return string The temporary password, for one-time display where email is not available yet.
     */
    public function handle(User $user): string
    {
        $temporaryPassword = Str::password(self::PASSWORD_LENGTH, symbols: false);

        $user->forceFill([
            'password' => $temporaryPassword,
            'password_change_required' => true,
            'temporary_password_expires_at' => now()->addHours(self::VALID_HOURS),
        ])->save();

        $user->notify(new AccountInvitation($temporaryPassword, $user->temporary_password_expires_at));

        return $temporaryPassword;
    }
}
