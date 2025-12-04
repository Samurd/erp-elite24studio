<?php

namespace App\Livewire\Modules\Rrhh\Employees;

use App\Livewire\Forms\Modules\Rrhh\Employees\Form as EmployeeForm;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public EmployeeForm $form;

    protected $listeners = [
        // 1. ESCUCHAR AL HIJO: Cuando termine de subir los archivos
        'attachments-committed' => 'finishCreation'
    ];


    public function save()
    {
        $employee = $this->form->store();

        $this->dispatch('commit-attachments', [
            'id' => $employee->id,
            'name' => $employee->full_name
        ]);

    }



    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('rrhh.employees.index');
    }


    public function render()
    {
        // Obtener tags de la base de datos
        $genderCategory = TagCategory::where('slug', 'genero')->first();
        $genders = $genderCategory ? Tag::where('category_id', $genderCategory->id)->get() : collect();

        $educationCategory = TagCategory::where('slug', 'educacion')->first();
        $educationTypes = $educationCategory ? Tag::where('category_id', $educationCategory->id)->get() : collect();

        $maritalStatusCategory = TagCategory::where('slug', 'estado_civil')->first();
        $maritalStatuses = $maritalStatusCategory ? Tag::where('category_id', $maritalStatusCategory->id)->get() : collect();

        // Obtener departamentos
        $departments = Department::orderBy('name')->get();

        return view('livewire.modules.rrhh.employees.create', [
            'genders' => $genders,
            'educationTypes' => $educationTypes,
            'maritalStatuses' => $maritalStatuses,
            'departments' => $departments,
        ]);
    }

}
