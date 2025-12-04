<?php

namespace App\Livewire\Forms\Modules\Rrhh\Interviews;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Interview;
use App\Models\Applicant;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class Form extends LivewireForm
{
    public $applicant_id;
    public $date;
    public $time;
    public $interviewer_id;
    public $interview_type_id;
    public $status_id;
    public $result_id;
    public $platform;
    public $platform_url;
    public $expected_results;
    public $interviewer_observations;
    public $rating;

    protected $rules = [
        'applicant_id' => 'required|exists:applicants,id',
        'date' => 'required|date',
        'time' => 'nullable|date_format:H:i',
        'interviewer_id' => 'nullable|exists:users,id',
        'interview_type_id' => 'nullable|exists:tags,id',
        'status_id' => 'nullable|exists:tags,id',
        'result_id' => 'nullable|exists:tags,id',
        'platform' => 'nullable|string|max:255',
        'platform_url' => 'nullable|url|max:500',
        'expected_results' => 'nullable|string',
        'interviewer_observations' => 'nullable|string',
        'rating' => 'nullable|numeric|min:0|max:10',
    ];

    public function store()
    {
        $this->validate();

        $interview = Interview::create([
            'applicant_id' => $this->applicant_id,
            'date' => $this->date,
            'time' => $this->time,
            'interviewer_id' => $this->interviewer_id ?: Auth::id(),
            'interview_type_id' => $this->interview_type_id,
            'status_id' => $this->status_id,
            'result_id' => $this->result_id,
            'platform' => $this->platform,
            'platform_url' => $this->platform_url,
            'expected_results' => $this->expected_results,
            'interviewer_observations' => $this->interviewer_observations,
            'rating' => $this->rating,
        ]);

        session()->flash('success', 'Entrevista creada exitosamente.');

        return redirect()->route('rrhh.interviews.index');
    }

    public function update(Interview $interview)
    {
        $this->validate();

        $interview->update([
            'applicant_id' => $this->applicant_id,
            'date' => $this->date,
            'time' => $this->time,
            'interviewer_id' => $this->interviewer_id,
            'interview_type_id' => $this->interview_type_id,
            'status_id' => $this->status_id,
            'result_id' => $this->result_id,
            'platform' => $this->platform,
            'platform_url' => $this->platform_url,
            'expected_results' => $this->expected_results,
            'interviewer_observations' => $this->interviewer_observations,
            'rating' => $this->rating,
        ]);

        session()->flash('success', 'Entrevista actualizada exitosamente.');

        return redirect()->route('rrhh.interviews.index');
    }

    public function setInterview(Interview $interview)
    {
        $this->applicant_id = $interview->applicant_id;
        $this->date = $interview->date->format('Y-m-d');
        $this->time = $interview->time ? $interview->time->format('H:i') : null;
        $this->interviewer_id = $interview->interviewer_id;
        $this->interview_type_id = $interview->interview_type_id;
        $this->status_id = $interview->status_id;
        $this->result_id = $interview->result_id;
        $this->platform = $interview->platform;
        $this->platform_url = $interview->platform_url;
        $this->expected_results = $interview->expected_results;
        $this->interviewer_observations = $interview->interviewer_observations;
        $this->rating = $interview->rating;
    }
}
