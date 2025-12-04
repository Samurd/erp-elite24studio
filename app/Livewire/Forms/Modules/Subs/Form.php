<?php

namespace App\Livewire\Forms\Modules\Subs;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Sub;

class Form extends LivewireForm
{
    public ?Sub $sub = null;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('nullable|exists:tags,id')]
    public $frequency_id = null;

    #[Validate('nullable|string')]
    public $type = '';

    #[Validate('required|integer|min:0')]
    public $amount = 0;

    #[Validate('nullable|date')]
    public $start_date = '';

    #[Validate('nullable|date')]
    public $renewal_date = '';

    #[Validate('nullable|exists:tags,id')]
    public $status_id = null;

    #[Validate('nullable|exists:users,id')]
    public $user_id = null;

    #[Validate('nullable|string')]
    public $notes = '';


    public function setSub(Sub $sub)
    {
        $this->sub = $sub;
        $this->name = $sub->name;
        $this->frequency_id = $sub->frequency_id;
        $this->type = $sub->type;
        $this->amount = $sub->amount;
        $this->start_date = $sub->start_date ? $sub->start_date->format('Y-m-d') : '';
        $this->renewal_date = $sub->renewal_date ? $sub->renewal_date->format('Y-m-d') : '';
        $this->status_id = $sub->status_id;
        $this->user_id = $sub->user_id;
        $this->notes = $sub->notes;
    }

    public function store()
    {
        $this->validate();

        $sub = Sub::create([
            'name' => $this->name,
            'frequency_id' => $this->frequency_id,
            'type' => $this->type,
            'amount' => $this->amount,
            'start_date' => $this->start_date,
            'renewal_date' => $this->renewal_date,
            'status_id' => $this->status_id,
            'user_id' => $this->user_id ?? auth()->id(),
            'notes' => $this->notes,
        ]);

        return $sub;
    }

    public function update()
    {
        $this->validate();

        $this->sub->update([
            'name' => $this->name,
            'frequency_id' => $this->frequency_id,
            'type' => $this->type,
            'amount' => $this->amount,
            'start_date' => $this->start_date,
            'renewal_date' => $this->renewal_date,
            'status_id' => $this->status_id,
            'user_id' => $this->user_id ?? auth()->id(),
            'notes' => $this->notes,
        ]);

    }

}
