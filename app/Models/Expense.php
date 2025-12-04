<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        "name",
        "category_id",
        "description",
        "amount",
        "result_id",
        "date",
        "created_by_id",
        // currency
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by_id");
    }

    public function category()
    {
        return $this->belongsTo(Tag::class, "category_id");
    }

    public function result()
    {
        return $this->belongsTo(Tag::class, "result_id");
    }
}
