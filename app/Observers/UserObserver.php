<?php

namespace App\Observers;

use App\Models\User;
use App\Services\CommonDataCacheService;
use App\Services\PermissionCacheService;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        CommonDataCacheService::clearType('users');

        // Clear permission caches as user might have roles/permissions
        PermissionCacheService::clearAll();
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        CommonDataCacheService::clearType('users');

        // If roles/permissions changed, clear those caches too
        if ($user->wasChanged(['email', 'name'])) {
            CommonDataCacheService::clearType('users');
        }

        // Clear user-specific area cache
        CommonDataCacheService::clearUserCache($user->id);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        CommonDataCacheService::clearType('users');
        CommonDataCacheService::clearUserCache($user->id);
    }
}
