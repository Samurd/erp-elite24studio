<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagCategory extends Model
{
    protected $fillable = ['slug','label','description'];

    public function tags()
    {
        return $this->hasMany(Tag::class, 'category_id');
    }
}
