<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kit extends Model
{

    protected $fillable = [
        'requested_by_user_id',
        'position_area',
        'recipient_name',
        'recipient_role',
        'kit_type',
        'kit_contents',
        'request_date',
        'delivery_date',
        'status_id',
        'delivery_responsible_user_id',
        'observations',
    ];

    protected $casts = [
        'request_date' => 'date',
        'delivery_date' => 'date',
    ];

    /**
     * Get the user who requested the kit.
     */
    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    /**
     * Get the status of the kit.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    /**
     * Get the user responsible for the delivery of the kit.
     */
    public function deliveryResponsibleUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_responsible_user_id');
    }

}
