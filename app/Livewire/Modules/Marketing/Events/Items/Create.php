<?php

namespace App\Livewire\Modules\Marketing\Events\Items;

use App\Livewire\Forms\Modules\Marketing\Events\Items\Form;
use App\Models\Event;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;

class Create extends Component
{
    public Form $form;
    public Event $event;

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->form->event_id = $event->id;
    }

    public function save()
    {
        $this->form->store();
        
        return redirect()->route('marketing.events.show', $this->event->id);
    }
    
    public function render()
    {
        // Obtener opciones para las unidades usando TagCategory
        $unitCategory = TagCategory::where('slug', 'unidad')->first();
        $unitOptions = $unitCategory ? Tag::where('category_id', $unitCategory->id)->get() : collect();

        return view('livewire.modules.marketing.events.items.create', [
            'event' => $this->event,
            'unitOptions' => $unitOptions,
        ]);
    }
}
