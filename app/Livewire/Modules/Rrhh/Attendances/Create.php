<?php

namespace App\Livewire\Modules\Rrhh\Attendances;

use Livewire\Component;
use App\Livewire\Forms\Modules\Rrhh\Attendances\Form;
use App\Models\Employee;
use App\Models\Tag;
use App\Models\TagCategory;

class Create extends Component
{
    public Form $form;

    public function mount()
    {
        // Set default date to today
        $this->form->date = date('Y-m-d');
    }

    public function save()
    {
        $this->form->store();

        session()->flash('success', 'Asistencia registrada exitosamente.');

        return redirect()->route('rrhh.attendances.index');
    }

    public function render()
    {
        // Get employees
        $employees = Employee::orderBy('full_name')->get();

        // Get status options
        $statusCategory = TagCategory::where('slug', 'estado_asistencia')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Get modality options
        $modalityCategory = TagCategory::where('slug', 'modalidad_trabajo')->first();
        $modalityOptions = $modalityCategory ? Tag::where('category_id', $modalityCategory->id)->get() : collect();

        return view('livewire.modules.rrhh.attendances.create', [
            'employees' => $employees,
            'statusOptions' => $statusOptions,
            'modalityOptions' => $modalityOptions,
        ]);
    }
}
