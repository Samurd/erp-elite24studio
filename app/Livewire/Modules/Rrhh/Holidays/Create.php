<?php

namespace App\Livewire\Modules\Rrhh\Holidays;

use App\Livewire\Forms\Modules\Rrhh\Holidays\Form;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Livewire\Component;

class Create extends Component
{
    public Form $form;

    protected $listeners = [
        // 1. ESCUCHAR AL HIJO: Cuando termine de subir los archivos
        'attachments-committed' => 'finishCreation'
    ];


    public function mount()
    {
        $this->form->start_date = date('Y-m-d');
        $this->form->end_date = date('Y-m-d');
        $this->form->approver_id = auth()->id();
    }

    public function save()
    {
        $holiday = $this->form->store();

        $holidayName = "Permisos de " . $holiday->employee->full_name;

        $this->dispatch('commit-attachments', [
            'id' => $holiday->id,
            'name' => $holidayName
        ]);
    }


    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('rrhh.holidays.index');
    }


    public function render()
    {
        // Get type options
        $typeCategory = TagCategory::where('slug', 'tipo_vacacion')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        // Get status options
        $statusCategory = TagCategory::where('slug', 'estado_vacacion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Get users
        $users = User::orderBy('name')->get();

        // Get employees
        $employees = \App\Models\Employee::orderBy('full_name')->get();

        return view('livewire.modules.rrhh.holidays.create', [
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'users' => $users,
            'employees' => $employees,
        ]);
    }
}
