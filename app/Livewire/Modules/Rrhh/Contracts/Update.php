<?php

namespace App\Livewire\Modules\Rrhh\Contracts;

use App\Livewire\Forms\Modules\Rrhh\Contracts\Form;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\File;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Update extends Component
{
    use WithFileUploads;

    public Form $form;
    public Contract $contract;

    public function mount(Contract $contract)
    {
        $this->contract = $contract;
        $this->form->setContract($contract);
    }

    public function save()
    {
        $this->form->update();

        session()->flash('success', 'Contrato actualizado exitosamente');

        return redirect()->route('rrhh.contracts.index');
    }


    public function render()
    {
        $employees = Employee::orderBy('first_name')->get();

        $typeCategory = TagCategory::where('slug', 'tipo_contrato_contratos')->first();
        $types = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        // Using tipo_relacion as requested
        $categoryCategory = TagCategory::where('slug', 'tipo_relacion')->first();
        $categories = $categoryCategory ? Tag::where('category_id', $categoryCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_contrato')->first();
        $statuses = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.rrhh.contracts.create', [
            'employees' => $employees,
            'types' => $types,
            'categories' => $categories,
            'statuses' => $statuses,
            'isEditing' => true,
            'contractFiles' => $this->contract->files,
        ]);
    }
}
