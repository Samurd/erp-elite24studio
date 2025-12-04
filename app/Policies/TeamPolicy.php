<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }


    public function manageMembers(User $user, Team $team): bool
    {
        return $team->isOwner($user);
    }

    public function updateTeam(User $user, Team $team): bool
    {
        return $team->isOwner($user);
    }

    public function view(User $user, Team $team): bool
    {
        // Puede ver si es miembro del equipo O si el equipo es público
        return $team->members()->where('user_id', $user->id)->exists() || $team->isPublic;
    }
}
