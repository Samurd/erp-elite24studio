<?php

namespace App\Livewire\Modules\Rrhh\Attendances;

use Livewire\Component;
use App\Livewire\Forms\Modules\Rrhh\Attendances\Form;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Tag;
use App\Models\TagCategory;

class Update extends Component
{
    public Form $form;
    public Attendance $attendance;

    public function mount(Attendance $attendance)
    {
        $this->attendance = $attendance;
        $this->form->setAttendance($attendance);
    }

    public function save()
    {
        $this->form->update();

        session()->flash('success', 'Asistencia actualizada exitosamente.');

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
