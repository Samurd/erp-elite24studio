<?php

namespace App\Observers;

use App\Models\Permission;
use App\Services\PermissionCacheService;
use App\Services\CommonDataCacheService;

class PermissionObserver
{
    /**
     * Handle the Permission "created" event.
     */
    public function created(Permission $permission): void
    {
        PermissionCacheService::clearAll();

        // Also clear user area caches as permissions affect area access
        // Note: This is a broad clear, but permissions don't change often
    }

    /**
     * Handle the Permission "updated" event.
     */
    public function updated(Permission $permission): void
    {
        PermissionCacheService::clearAll();
    }

    /**
     * Handle the Permission "deleted" event.
     */
    public function deleted(Permission $permission): void
    {
        PermissionCacheService::clearAll();
    }
}
