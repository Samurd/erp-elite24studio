<?php

namespace App\Livewire\Modules\Subs;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Livewire\Forms\Modules\Subs\Form;
use App\Models\Tag;
use App\Models\TagCategory;

class Create extends Component
{
    use WithFileUploads;

    public Form $form;


    protected $listeners = [
        // 1. ESCUCHAR AL HIJO: Cuando termine de subir los archivos
        'attachments-committed' => 'finishCreation'
    ];



    public function save()
    {
        $sub = $this->form->store();

        $this->dispatch('commit-attachments', [
            'id' => $sub->id,
            'name' => $sub->name
        ]);
    }

    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('subs.index');
    }

    public function render()
    {
        $statusCategory = TagCategory::where('slug', 'estado_suscripcion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $frequencyCategory = TagCategory::where('slug', 'frecuencia_sub')->first();
        $frequencyOptions = $frequencyCategory ? Tag::where('category_id', $frequencyCategory->id)->get() : collect();

        return view('livewire.modules.subs.create', [
            'statusOptions' => $statusOptions,
            'frequencyOptions' => $frequencyOptions,
            'isEdit' => false,
        ]);
    }
}
