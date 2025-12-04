<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasRoles;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function meetingsResponsible()
    {
        return $this->belongsToMany(Meeting::class, 'meeting_responsibles');
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_user')
            ->withPivot('role_id')
            ->withTimestamps();
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_user')->withTimestamps();
    }

    /**
     * Get all private chats for this user.
     */
    public function privateChats()
    {
        return $this->belongsToMany(PrivateChat::class, 'private_chat_user')->withTimestamps();
    }

    /**
     * Get or create a chat with another user.
     */
    public function chatWith($otherUserId)
    {
        // Buscar chat existente entre ambos usuarios
        $existingChat = PrivateChat::whereHas('participants', function ($q) use ($otherUserId) {
            $q->where('user_id', $otherUserId);
        })
            ->whereHas('participants', function ($q) {
                $q->where('user_id', $this->id);
            })
            ->where('is_group', false)
            ->first();

        if ($existingChat) {
            return $existingChat;
        }

        // Crear nuevo chat
        $chat = PrivateChat::create([
            'is_group' => false,
            'name' => null
        ]);

        $chat->participants()->attach([$this->id, $otherUserId]);

        return $chat;
    }

}
