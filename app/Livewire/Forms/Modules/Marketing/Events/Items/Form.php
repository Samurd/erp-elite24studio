<?php

namespace App\Livewire\Forms\Modules\Marketing\Events\Items;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\EventItem;

class Form extends LivewireForm
{
    #[Validate('required|string|max:255')]
    public $description = '';

    #[Validate('required|numeric|min:0')]
    public $quantity = '';

    #[Validate('required|exists:tags,id')]
    public $unit_id = '';

    #[Validate('required|numeric|min:0')]
    public $unit_price = '';

    #[Validate('required|numeric|min:0')]
    public $total_price = '';

    public $event_id = '';


    public function store()
    {
        $this->validate();

        EventItem::create($this->all());

        $this->reset();
    }

    public function setEventItem(EventItem $eventItem)
    {
        $this->description = $eventItem->description;
        $this->quantity = $eventItem->quantity;
        $this->unit_id = $eventItem->unit_id;
        $this->unit_price = $eventItem->unit_price;
        $this->total_price = $eventItem->total_price;
        $this->event_id = $eventItem->event_id;
    }

    public function update(EventItem $eventItem)
    {
        $this->validate();

        $eventItem->update($this->all());
    }
}
