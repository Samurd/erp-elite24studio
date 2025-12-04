<?php

namespace App\Models;

use App\Traits\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFiles;
    protected $fillable = [
        "name",
        "description",
        "direction",
        "contact_id",
        "status_id",
        "project_type_id",
        "current_stage_id",
        "responsible_id",
        "team_id",
    ];

    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function status()
    {
        return $this->belongsTo(Tag::class, "status_id");
    }

    public function projectType()
    {
        return $this->belongsTo(Tag::class, "project_type_id");
    }

    public function currentStage()
    {
        return $this->belongsTo(Stage::class, "current_stage_id");
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, "responsible_id");
    }

    public function team()
    {
        return $this->belongsTo(Team::class, "team_id");
    }

    public function stages()
    {
        return $this->hasMany(Stage::class, "project_id");
    }

}
