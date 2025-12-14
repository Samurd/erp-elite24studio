<?php

namespace App\Observers;

use App\Models\Team;
use App\Services\CommonDataCacheService;

class TeamObserver
{
    /**
     * Handle the Team "created" event.
     */
    public function created(Team $team): void
    {
        CommonDataCacheService::clearType('teams');
    }

    /**
     * Handle the Team "updated" event.
     */
    public function updated(Team $team): void
    {
        CommonDataCacheService::clearType('teams');
    }

    /**
     * Handle the Team "deleted" event.
     */
    public function deleted(Team $team): void
    {
        CommonDataCacheService::clearType('teams');
    }
}
