<?php

namespace App\Livewire\Modules\Marketing\Events;

use App\Livewire\Forms\Modules\Marketing\Events\Form;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Livewire\Component;

class Create extends Component
{

    protected $listeners = [
        // 1. ESCUCHAR AL HIJO: Cuando termine de subir los archivos
        'attachments-committed' => 'finishCreation'
    ];

    public Form $form;

    public function save()
    {

        $event = $this->form->store();


        $this->dispatch('commit-attachments', [
            'id' => $event->id,
            'name' => $event->piece_name
        ]);

    }

    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

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
        ]);
    }
}
