<?php

namespace App\Livewire\Forms\Modules\Marketing\Strategies;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Strategy;

class Form extends LivewireForm
{
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('nullable|string|max:500')]
    public $objective = '';

    #[Validate('nullable|exists:tags,id')]
    public $status_id = '';

    #[Validate('nullable|date')]
    public $start_date = '';

    #[Validate('nullable|date|after_or_equal:start_date')]
    public $end_date = '';

    #[Validate('nullable|string|max:255')]
    public $target_audience = '';

    #[Validate('nullable|string|max:255')]
    public $platforms = '';

    #[Validate('nullable|exists:users,id')]
    public $responsible_id = '';

    #[Validate('boolean')]
    public $notify_team = false;

    #[Validate('boolean')]
    public $add_to_calendar = false;

    #[Validate('nullable|string|max:1000')]
    public $observations = '';

    public function store()
    {
        $this->validate();

        Strategy::create([
            'name' => $this->name,
            'objective' => $this->objective,
            'status_id' => $this->status_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'target_audience' => $this->target_audience,
            'platforms' => $this->platforms,
            'responsible_id' => $this->responsible_id,
            'notify_team' => $this->notify_team,
            'add_to_calendar' => $this->add_to_calendar,
            'observations' => $this->observations,
        ]);

        $this->reset();
    }

    public function update(Strategy $strategy)
    {
        $this->validate();

        $strategy->update([
            'name' => $this->name,
            'objective' => $this->objective,
            'status_id' => $this->status_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'target_audience' => $this->target_audience,
            'platforms' => $this->platforms,
            'responsible_id' => $this->responsible_id,
            'notify_team' => $this->notify_team,
            'add_to_calendar' => $this->add_to_calendar,
            'observations' => $this->observations,
        ]);
    }

    public function setStrategy(Strategy $strategy)
    {
        $this->name = $strategy->name;
        $this->objective = $strategy->objective;
        $this->status_id = $strategy->status_id;
        $this->start_date = $strategy->start_date?->format('Y-m-d');
        $this->end_date = $strategy->end_date?->format('Y-m-d');
        $this->target_audience = $strategy->target_audience;
        $this->platforms = $strategy->platforms;
        $this->responsible_id = $strategy->responsible_id;
        $this->notify_team = $strategy->notify_team;
        $this->add_to_calendar = $strategy->add_to_calendar;
        $this->observations = $strategy->observations;
    }
}
