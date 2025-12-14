<?php

namespace App\Observers;

use App\Models\Stage;
use App\Services\CommonDataCacheService;

class StageObserver
{
    /**
     * Handle the Stage "created" event.
     */
    public function created(Stage $stage): void
    {
        CommonDataCacheService::clearType('stages');
    }

    /**
     * Handle the Stage "updated" event.
     */
    public function updated(Stage $stage): void
    {
        CommonDataCacheService::clearType('stages');
    }

    /**
     * Handle the Stage "deleted" event.
     */
    public function deleted(Stage $stage): void
    {
        CommonDataCacheService::clearType('stages');
    }
}
