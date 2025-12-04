<?php

namespace App\Policies;

use App\Models\Area;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AreaPolicy
{

    protected function buildPermissionName(Area $area, string $action): string
    {
        if ($area->parent) {
            return $area->parent->slug . '.' . $area->slug . '.' . $action;
        }

        return $area->slug . '.' . $action;
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Area $area): bool
    {
        return $user->can($this->buildPermissionName($area, 'view'));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Area $area): bool
    {
        return $user->can($this->buildPermissionName($area, 'create'));
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Area $area): bool
    {
        return $user->can($this->buildPermissionName($area, 'update'));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Area $area): bool
    {
        return $user->can($this->buildPermissionName($area, 'delete'));
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Area $area): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Area $area): bool
    {
        return false;
    }
}
