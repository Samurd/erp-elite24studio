<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'company',
        'contact_type_id',
        'email',
        'email_personal',
        'email_corporativo',
        'phone',
        'address',
        'city',
        // 'associated_project',
        'first_contact_date',
        'notes',
        'status_id',
        'relation_type_id',
        'source_id',
        'label_id',
        'responsible_id',
    ];

    /**
     * Relaciones con tags
     */

    public function contactType()
    {
        return $this->belongsTo(Tag::class, 'contact_type_id');
    }
    public function status()
    {
        return $this->belongsTo(Tag::class, 'status_id');
    }

    public function relationType()
    {
        return $this->belongsTo(Tag::class, 'relation_type_id');
    }

    public function source()
    {
        return $this->belongsTo(Tag::class, 'source_id');
    }

    public function label()
    {
        return $this->belongsTo(Tag::class, 'label_id');
    }

    /**
     * Responsable (usuario asignado al contacto)
     */
    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    /**
     * Casos relacionados con este contacto
     */
    public function cases()
    {
        return $this->hasMany(CaseRecord::class);
    }
}
