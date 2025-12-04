<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFiles;

    protected $fillable = [
        // Work Information
        'full_name',
        'job_title',
        'work_email',
        'mobile_phone',
        'curriculum_file',
        'work_address',
        'work_schedule',
        'department_id',

        // Private Information
        'home_address',
        'personal_email',
        'private_phone',
        'bank_account',
        'bank_certificate_file',
        'identification_number',
        'social_security_number',
        'passport_number',
        'gender_id',
        'birth_date',
        'birth_place',
        'birth_country',
        'has_disability',
        'disability_details',

        // Emergency Contact
        'emergency_contact_name',
        'emergency_contact_phone',

        // Education
        'education_type_id',

        // Family Status
        'marital_status_id',
        'number_of_dependents',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'has_disability' => 'boolean',
        'number_of_dependents' => 'integer',
    ];

    protected $dates = [
        'birth_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the work email address (lowercase).
     *
     * @param  string  $value
     * @return string
     */
    public function getWorkEmailAttribute($value)
    {
        return strtolower($value);
    }

    /**
     * Set the work email address (lowercase).
     *
     * @param  string  $value
     * @return void
     */
    public function setWorkEmailAttribute($value)
    {
        $this->attributes['work_email'] = strtolower($value);
    }

    /**
     * Get the personal email address (lowercase).
     *
     * @param  string  $value
     * @return string
     */
    public function getPersonalEmailAttribute($value)
    {
        return strtolower($value);
    }

    /**
     * Set the personal email address (lowercase).
     *
     * @param  string  $value
     * @return void
     */
    public function setPersonalEmailAttribute($value)
    {
        $this->attributes['personal_email'] = strtolower($value);
    }

    /**
     * Get the full name with proper formatting.
     *
     * @return string
     */
    public function getFormattedFullNameAttribute()
    {
        return ucwords(strtolower($this->full_name));
    }

    /**
     * Scope a query to only include active employees.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope a query to filter by job title.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $jobTitle
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByJobTitle($query, $jobTitle)
    {
        return $query->where('job_title', 'like', "%{$jobTitle}%");
    }

    /**
     * Scope a query to filter by education type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $educationTypeId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByEducationType($query, $educationTypeId)
    {
        return $query->where('education_type_id', $educationTypeId);
    }

    /**
     * Get the gender tag associated with the employee.
     */
    public function gender()
    {
        return $this->belongsTo(Tag::class, 'gender_id');
    }

    /**
     * Get the education type tag associated with the employee.
     */
    public function educationType()
    {
        return $this->belongsTo(Tag::class, 'education_type_id');
    }

    /**
     * Get the marital status tag associated with the employee.
     */
    public function maritalStatus()
    {
        return $this->belongsTo(Tag::class, 'marital_status_id');
    }


    /**
     * Scope a query to filter by gender.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $genderId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByGender($query, $genderId)
    {
        return $query->where('gender_id', $genderId);
    }

    /**
     * Scope a query to filter by marital status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $maritalStatusId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByMaritalStatus($query, $maritalStatusId)
    {
        return $query->where('marital_status_id', $maritalStatusId);
    }

    /**
     * Get the department that owns the employee.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Scope a query to filter by department.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $departmentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}
