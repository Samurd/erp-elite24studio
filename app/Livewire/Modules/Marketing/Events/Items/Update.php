<?php

namespace App\Livewire\Modules\Marketing\Events\Items;

use App\Livewire\Forms\Modules\Marketing\Events\Items\Form;
use App\Models\Event;
use App\Models\EventItem;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    public Event $event;
    public EventItem $eventItem;

    public function mount(Event $event, EventItem $eventItem)
    {
        $this->event = $event;
        $this->eventItem = $eventItem;
        $this->form->setEventItem($eventItem);
    }

    public function save()
    {
        $this->form->update($this->eventItem);
        
        return redirect()->route('marketing.events.show', $this->event->id);
    }

    public function render()
    {
        // Obtener opciones para las unidades usando TagCategory
        $unitCategory = TagCategory::where('slug', 'unidad')->first();
        $unitOptions = $unitCategory ? Tag::where('category_id', $unitCategory->id)->get() : collect();

        return view('livewire.modules.marketing.events.items.create', [
            'event' => $this->event,
            'eventItem' => $this->eventItem,
            'unitOptions' => $unitOptions,
        ]);
    }
}
