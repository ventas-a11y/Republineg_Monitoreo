<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Accounts are managed only by Administradores. Everyone else gets a 404 so
 * the panel does not reveal which accounts exist (docs/SEGURIDAD.md, IDOR).
 */
class UserPolicy
{
    /**
     * Determine whether the user can list accounts.
     */
    public function viewAny(User $user): Response
    {
        return $this->administradorOnly($user);
    }

    /**
     * Determine whether the user can create accounts.
     */
    public function create(User $user): Response
    {
        return $this->administradorOnly($user);
    }

    /**
     * Determine whether the user can edit the account.
     */
    public function update(User $user, User $model): Response
    {
        return $this->administradorOnly($user);
    }

    /**
     * Determine whether the user can send a new invitation for the account.
     */
    public function resendInvitation(User $user, User $model): Response
    {
        return $this->administradorOnly($user);
    }

    private function administradorOnly(User $user): Response
    {
        return $user->isAdministrador() ? Response::allow() : Response::denyAsNotFound();
    }
}
