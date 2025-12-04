<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{

    protected $fillable = ['name', 'description', 'isPublic'];

    /**
     * Relation: A Team has many members.
     * Pivot table: team_user
     *
     * Todos los usuarios que pertenecen a un equipo.
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'team_user')
            ->withPivot('role_id')
            ->withTimestamps();
    }



    /**
     * Relation: A Team has many channels.
     *
     * Estos canales son tipo "Slack / Teams".
     */
    public function channels()
    {
        return $this->hasMany(TeamChannel::class);
    }


    /**
     * Obtiene el role_id del usuario dentro del equipo
     */
    public function roleIdOf(User $user): ?int
    {
        $m = $this->members()->where('user_id', $user->id)->first();
        return $m ? $m->pivot->role_id : null;
    }

    /**
     * Obtiene el slug del rol del usuario
     */
    public function roleSlugOf(User $user): ?string
    {
        $roleId = $this->roleIdOf($user);
        if (!$roleId)
            return null;

        return TeamRole::find($roleId)?->slug;
    }

    /**
     * Verifica si un usuario es Owner
     */
    public function isOwner(User $user): bool
    {
        return $this->roleSlugOf($user) === 'owner';
    }

    /**
     * Verifica si el usuario es Member
     */
    public function isMember(User $user): bool
    {
        return $this->roleSlugOf($user) === 'member';
    }


    public function plans()
    {
        return $this->hasMany(Plan::class);
    }
}
