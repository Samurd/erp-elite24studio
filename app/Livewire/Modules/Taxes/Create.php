<?php

namespace App\Livewire\Modules\Taxes;

use Livewire\Component;
use App\Livewire\Forms\Modules\Taxes\Form;
use App\Models\Tag;
use App\Models\TagCategory;

class Create extends Component
{
    use \Livewire\WithFileUploads;

    public Form $form;

    protected $listeners = [
        // 1. ESCUCHAR AL HIJO: Cuando termine de subir los archivos
        'attachments-committed' => 'finishCreation'
    ];


    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('finances.taxes.index');
    }

    public function save()
    {


        $taxe = $this->form->store();

        $nameTaxe = "Impuesto id: " . $taxe->id . " - Entidad: " . $taxe->entity;

        $this->dispatch('commit-attachments', [
            'id' => $taxe->id,
            'name' => $nameTaxe
        ]);

    }

    public function render()
    {
        $typeCategory = TagCategory::where('slug', 'tipo_impuesto')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_impuesto')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.taxes.create', [
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
        ]);
    }
}
