<?php

namespace App\Policies;

use App\Models\Area;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * Áreas are configured only by Administradores; everyone else gets a 404.
 */
class AreaPolicy
{
    /**
     * Determine whether the user can list Áreas.
     */
    public function viewAny(User $user): Response
    {
        return $this->administradorOnly($user);
    }

    /**
     * Determine whether the user can create Áreas.
     */
    public function create(User $user): Response
    {
        return $this->administradorOnly($user);
    }

    /**
     * Determine whether the user can rename the Área.
     */
    public function update(User $user, Area $area): Response
    {
        return $this->administradorOnly($user);
    }

    private function administradorOnly(User $user): Response
    {
        return $user->isAdministrador() ? Response::allow() : Response::denyAsNotFound();
    }
}
