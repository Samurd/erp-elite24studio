<?php

namespace App\Livewire\Forms\Modules\Marketing\Events;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Event;

class Form extends LivewireForm
{
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|exists:tags,id')]
    public $type_id = '';

    #[Validate('required|date')]
    public $event_date = '';

    #[Validate('required|string|max:255')]
    public $location = '';

    #[Validate('required|exists:tags,id')]
    public $status_id = '';

    #[Validate('required|exists:users,id')]
    public $responsible_id = '';

    #[Validate('nullable|string')]
    public $observations = '';

    public function store()
    {
        $this->validate();

        $event = Event::create($this->all());

        return $event;
    }

    public function setEvent(Event $event)
    {
        $this->name = $event->name;
        $this->type_id = $event->type_id;
        $this->event_date = $event->event_date?->format('Y-m-d');
        $this->location = $event->location;
        $this->status_id = $event->status_id;
        $this->responsible_id = $event->responsible_id;
        $this->observations = $event->observations;
    }

    public function update(Event $event)
    {
        $this->validate();

        $event->update($this->all());
    }
}
