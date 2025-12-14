<?php

namespace App\Observers;

use App\Models\Contact;
use App\Services\CommonDataCacheService;

class ContactObserver
{
    /**
     * Handle the Contact "created" event.
     */
    public function created(Contact $contact): void
    {
        CommonDataCacheService::clearType('contacts');
    }

    /**
     * Handle the Contact "updated" event.
     */
    public function updated(Contact $contact): void
    {
        CommonDataCacheService::clearType('contacts');
    }

    /**
     * Handle the Contact "deleted" event.
     */
    public function deleted(Contact $contact): void
    {
        CommonDataCacheService::clearType('contacts');
    }
}
