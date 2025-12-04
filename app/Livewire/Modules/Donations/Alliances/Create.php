<?php

namespace App\Livewire\Modules\Donations\Alliances;

use App\Livewire\Forms\Modules\Donations\Alliances\Form;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;

class Create extends Component
{
    public Form $form;

    protected $listeners = [
        // 1. ESCUCHAR AL HIJO: Cuando termine de subir los archivos
        'attachments-committed' => 'finishCreation'
    ];


    public function save()
    {
        $alliance = $this->form->store();

        $this->dispatch('commit-attachments', [
            'id' => $alliance->id,
            'name' => $alliance->name
        ]);


    }

    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('donations.alliances.index');
    }

    public function render()
    {
        // Obtener opciones para el tipo de alianza
        $typeCategory = TagCategory::where('slug', 'tipo_alianza')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        return view('livewire.modules.donations.alliances.create', [
            'typeOptions' => $typeOptions,
        ]);
    }
}
