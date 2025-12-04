<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = [
        "name",
        "type_id",
        "category_id",
        "description",
        "amount",
        "result_id",
        "date",
        "created_by_id",
        // "currency",
    ];

    public static function moneyFormatter($amount)
    {
        $value = $amount / 100; // convertir de centavos a pesos
        return '$' . number_format($value, 2, ",", ".");
    }

    public function getAmountFormattedAttribute(): string
    {
        $value = $this->amount / 100; // convertir de centavos a pesos
        return '$' . number_format($value, 2, ",", "."); // Ej: $1.234,56
    }

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

    public function type()
    {
        return $this->belongsTo(Tag::class, "type_id");
    }

    public function result()
    {
        return $this->belongsTo(Tag::class, "result_id");
    }
}
