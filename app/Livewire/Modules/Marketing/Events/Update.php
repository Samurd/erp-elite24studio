<?php

namespace App\Livewire\Modules\Marketing\Events;

use App\Livewire\Forms\Modules\Marketing\Events\Form;
use App\Models\Event;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    public Event $event;

    public function mount(Event $event)
    {
        $this->event = $event;
        $this->form->setEvent($event);
    }

    public function save()
    {
        $this->form->update($this->event);
        
        return redirect()->route('marketing.events.index');
    }

    public function render()
    {
        // Obtener opciones para los filtros usando TagCategory
        $eventTypeCategory = TagCategory::where('slug', 'tipo_evento')->first();
        $eventTypeOptions = $eventTypeCategory ? Tag::where('category_id', $eventTypeCategory->id)->get() : collect();
        
        $statusCategory = TagCategory::where('slug', 'estado_evento')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        
        // Obtener usuarios para el selector de responsables
        $users = User::orderBy('name')->get();

        return view('livewire.modules.marketing.events.create', [
            'eventTypeOptions' => $eventTypeOptions,
            'statusOptions' => $statusOptions,
            'users' => $users,
            'event' => $this->event,
        ]);
    }
}
