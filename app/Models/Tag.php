<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['category_id','name','slug','color'];

    public function category()
    {
        return $this->belongsTo(TagCategory::class, 'category_id');
    }

    // scope útil
    public function scopeOfCategoryName($query, $categoryName)
    {
        return $query->whereHas('category', fn($q) => $q->where('name', $categoryName));
    }
}
