<?php

namespace App\Livewire\Modules\Rrhh\Employees;

use App\Livewire\Forms\Modules\Rrhh\Employees\Form as EmployeeForm;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;
use Livewire\WithFileUploads;

class Update extends Component
{
    use WithFileUploads;

    public EmployeeForm $form;
    public Employee $employee;


    public function mount(Employee $employee)
    {
        $this->employee = $employee;
        $this->form->setEmployee($employee);
    }

    public function save()
    {
        $this->form->update();

        session()->flash('message', 'Empleado actualizado exitosamente.');

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
