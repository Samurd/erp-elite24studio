<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_date',
        'code',
        'contact_id',
        'description',
        'created_by_id',
        'total',
        'method_payment',
        'status_id',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'total' => 'integer',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    use \App\Traits\HasFiles;

    protected function getDefaultFolderName(): string
    {
        return 'invoices';
    }

}
