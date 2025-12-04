<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Norm extends Model
{
    use HasFiles;

    protected $fillable = [
        'name',
        'user_id',
    ];

    protected function getDefaultFolderName(): string
    {
        return 'norms';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
