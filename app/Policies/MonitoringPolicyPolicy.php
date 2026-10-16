<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Publishing the Política de monitoreo and seeing who accepted it is for
 * Administradores; everyone else gets a 404.
 */
class MonitoringPolicyPolicy
{
    /**
     * Determine whether the user can see versions and acceptance status.
     */
    public function viewAny(User $user): Response
    {
        return $this->administradorOnly($user);
    }

    /**
     * Determine whether the user can publish a new version.
     */
    public function create(User $user): Response
    {
        return $this->administradorOnly($user);
    }

    private function administradorOnly(User $user): Response
    {
        return $user->isAdministrador() ? Response::allow() : Response::denyAsNotFound();
    }
}
