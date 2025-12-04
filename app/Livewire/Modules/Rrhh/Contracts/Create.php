<?php

namespace App\Livewire\Modules\Rrhh\Contracts;

use App\Livewire\Forms\Modules\Rrhh\Contracts\Form;
use App\Models\Employee;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;
use Livewire\WithFileUploads;

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


        $contract = $this->form->store();

        $this->dispatch('commit-attachments', [
            'id' => $contract->id,
            'name' => $contract->name
        ]);

    }

    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('rrhh.contracts.index');
    }


    public function render()
    {
        $employees = Employee::orderBy('full_name')->get();

        $typeCategory = TagCategory::where('slug', 'tipo_contrato_contratos')->first();
        $types = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        // Using tipo_relacion as requested instead of categoria_contrato
        $categoryCategory = TagCategory::where('slug', 'tipo_relacion')->first();
        $categories = $categoryCategory ? Tag::where('category_id', $categoryCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_contrato')->first();
        $statuses = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.rrhh.contracts.create', [
            'employees' => $employees,
            'types' => $types,
            'categories' => $categories,
            'statuses' => $statuses,
        ]);
    }
}
