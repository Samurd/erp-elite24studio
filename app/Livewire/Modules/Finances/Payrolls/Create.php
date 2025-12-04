<?php

namespace App\Livewire\Modules\Finances\Payrolls;

use Livewire\Component;
use Livewire\WithFileUploads;

use App\Livewire\Forms\Modules\Finances\Payrolls\Form;
use App\Models\Employee;
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


    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('finances.payrolls.index');
    }

    public function save()
    {


        $payroll = $this->form->store();

        $namePayroll = "Nomina con id:  " . $payroll->id . " - " . ($payroll->employee ? $payroll->employee->full_name : 'Sin Empleado');

        $this->dispatch('commit-attachments', [
            'id' => $payroll->id,
            'name' => $namePayroll
        ]);

    }

    public function render()
    {
        $employees = Employee::active()->orderBy('full_name')->get();

        $statusCategory = TagCategory::where('slug', 'estado_nomina')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.finances.payrolls.create', [
            'employees' => $employees,
            'statusOptions' => $statusOptions,
            'isEdit' => false,
        ]);
    }
}
