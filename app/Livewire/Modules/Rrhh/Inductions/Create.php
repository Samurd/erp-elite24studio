<?php

namespace App\Livewire\Modules\Rrhh\Inductions;

use Livewire\Component;
use App\Models\Induction;
use App\Models\Employee;
use App\Models\User;
use App\Models\Tag;
use App\Models\TagCategory;

class Create extends Component
{
    public $employee_id;
    public $type_bond_id;
    public $entry_date;
    public $responsible_id;
    public $date;
    public $status_id;
    public $confirmation_id;
    public $resource;
    public $duration;
    public $observations;

    public function mount()
    {
        $this->entry_date = date('Y-m-d');
        $this->date = date('Y-m-d');
    }

    protected $rules = [
        'employee_id' => 'required|exists:employees,id',
        'type_bond_id' => 'nullable|exists:tags,id',
        'entry_date' => 'required|date',
        'responsible_id' => 'nullable|exists:users,id',
        'date' => 'nullable|date',
        'status_id' => 'nullable|exists:tags,id',
        'confirmation_id' => 'nullable|exists:tags,id',
        'resource' => 'nullable|string|max:255',
        'duration' => 'nullable|date_format:H:i',
        'observations' => 'nullable|string|max:1000',
    ];

    public function save()
    {
        $this->validate();

        Induction::create([
            'employee_id' => $this->employee_id,
            'type_bond_id' => $this->type_bond_id,
            'entry_date' => $this->entry_date,
            'responsible_id' => $this->responsible_id,
            'date' => $this->date,
            'status_id' => $this->status_id,
            'confirmation_id' => $this->confirmation_id,
            'resource' => $this->resource,
            'duration' => $this->duration,
            'observations' => $this->observations,
        ]);

        session()->flash('success', 'Inducción creada exitosamente.');

        return redirect()->route('rrhh.inductions.index');
    }

    public function render()
    {
        // Obtener opciones para los filtros usando TagCategory
        $typeBondCategory = TagCategory::where('slug', 'tipo_vinculo')->first();
        $typeBondOptions = $typeBondCategory ? Tag::where('category_id', $typeBondCategory->id)->get() : collect();
        
        $statusCategory = TagCategory::where('slug', 'estado_induccion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        
        $confirmationCategory = TagCategory::where('slug', 'confirmacion_induccion')->first();
        $confirmationOptions = $confirmationCategory ? Tag::where('category_id', $confirmationCategory->id)->get() : collect();

        // Obtener empleados y responsables
        $employees = Employee::orderBy('full_name')->get();
        $responsibles = User::orderBy('name')->get();

        return view('livewire.modules.rrhh.inductions.create', [
            'typeBondOptions' => $typeBondOptions,
            'statusOptions' => $statusOptions,
            'confirmationOptions' => $confirmationOptions,
            'employees' => $employees,
            'responsibles' => $responsibles,
        ]);
    }
}