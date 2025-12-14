<?php

namespace App\Livewire\Modules\Meetings;

use Livewire\Component;
use App\Models\Meeting;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Team;
use App\Models\User;
use Livewire\WithFileUploads;

class Update extends Component
{
    use WithFileUploads;

    public $meeting;
    public $title = '';
    public $date = '';
    public $start_time = '';
    public $end_time = '';
    public $team_id = '';
    public $status_id = '';
    public $notes = '';
    public $observations = '';
    public $url = '';
    public $goal = false;
    public $responsibles = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'date' => 'required|date',
        'start_time' => 'required',
        'end_time' => 'required|after:start_time',
        'team_id' => 'nullable|exists:teams,id',
        'status_id' => 'nullable|exists:tags,id',
        'notes' => 'nullable|string',
        'observations' => 'nullable|string',
        'url' => 'nullable|url',
        'goal' => 'boolean',
        'responsibles' => 'nullable|array',
        'responsibles.*' => 'exists:users,id',
    ];

    protected $messages = [
        'title.required' => 'El título es obligatorio',
        'date.required' => 'La fecha es obligatoria',
        'start_time.required' => 'La hora de inicio es obligatoria',
        'end_time.required' => 'La hora de fin es obligatoria',
        'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio',
        'team_id.exists' => 'El equipo seleccionado no es válido',
        'status_id.exists' => 'El estado seleccionado no es válido',
        'url.url' => 'La URL debe ser una dirección válida',
        'responsibles.*.exists' => 'Uno de los responsables seleccionados no es válido',
    ];

    public function mount(Meeting $meeting)
    {
        $this->meeting = $meeting;
        $this->title = $meeting->title;
        $this->date = $meeting->date ? $meeting->date->format('Y-m-d') : '';
        $this->start_time = $meeting->start_time ? $meeting->start_time->format('H:i') : '';
        $this->end_time = $meeting->end_time ? $meeting->end_time->format('H:i') : '';
        $this->team_id = $meeting->team_id;
        $this->status_id = $meeting->status_id;
        $this->notes = $meeting->notes;
        $this->observations = $meeting->observations;
        $this->url = $meeting->url;
        $this->goal = $meeting->goal;
        $this->responsibles = $meeting->responsibles->pluck('id')->toArray();
    }

    public function update()
    {
        $this->validate();

        $this->meeting->update([
            'title' => $this->title,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'team_id' => $this->team_id ?: null,
            'status_id' => $this->status_id ?: null,
            'notes' => $this->notes,
            'observations' => $this->observations,
            'url' => $this->url,
            'goal' => $this->goal,
        ]);

        // Sync responsibles
        $this->meeting->responsibles()->sync($this->responsibles);

        session()->flash('success', 'Reunión actualizada exitosamente.');

        return redirect()->route('meetings.show', $this->meeting->id);
    }

    public function render()
    {
        // Get status options
        $statusCategory = TagCategory::where('slug', 'estado_reunion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Get team options (cached)
        $teamOptions = \App\Services\CommonDataCacheService::getAllTeams();

        // Get user options for responsibles (cached)
        $userOptions = \App\Services\CommonDataCacheService::getAllUsers();

        return view('livewire.modules.meetings.update', [
            'statusOptions' => $statusOptions,
            'teamOptions' => $teamOptions,
            'userOptions' => $userOptions,
        ]);
    }
}
