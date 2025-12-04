<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFiles;

    protected $fillable = [
        'employee_id',
        'subtotal',
        'bonos',
        'deductions',
        'total',
        'status_id',
        'observations',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'bonos' => 'integer',
        'deductions' => 'integer',
        'total' => 'integer',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function status()
    {
        return $this->belongsTo(Tag::class);
    }

    /**
     * Define la carpeta por defecto para archivos de nóminas
     */
    protected function getDefaultFolderName(): string
    {
        return 'payrolls';
    }
}
