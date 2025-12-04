<?php

namespace App\Livewire\Modules\Rrhh\Inductions;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Induction;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Employee;
use App\Models\User;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $employee_filter = '';
    public $type_bond_filter = '';
    public $status_filter = '';
    public $confirmation_filter = '';
    public $responsible_filter = '';
    public $date_from = '';
    public $date_to = '';
    public $entry_date_from = '';
    public $entry_date_to = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'employee_filter' => ['except' => ''],
        'type_bond_filter' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'confirmation_filter' => ['except' => ''],
        'responsible_filter' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
        'entry_date_from' => ['except' => ''],
        'entry_date_to' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingEmployeeFilter()
    {
        $this->resetPage();
    }

    public function updatingTypeBondFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingConfirmationFilter()
    {
        $this->resetPage();
    }

    public function updatingResponsibleFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function updatingEntryDateFrom()
    {
        $this->resetPage();
    }

    public function updatingEntryDateTo()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'employee_filter',
            'type_bond_filter',
            'status_filter',
            'confirmation_filter',
            'responsible_filter',
            'date_from',
            'date_to',
            'entry_date_from',
            'entry_date_to',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $induction = Induction::find($id);
        
        if ($induction) {
            $induction->delete();
            session()->flash('success', 'Inducción eliminada exitosamente.');
        }
    }

    public function render()
    {
        $query = Induction::with([
            'employee',
            'typeBond',
            'responsible',
            'status',
            'confirmation'
        ]);

        // Search by employee name or observations
        if ($this->search) {
            $query->where('observations', 'like', '%' . $this->search . '%')
                  ->orWhereHas('employee', function($q) {
                      $q->where('full_name', 'like', '%' . $this->search . '%');
                  });
        }

        // Filter by employee
        if ($this->employee_filter) {
            $query->where('employee_id', $this->employee_filter);
        }

        // Filter by type bond
        if ($this->type_bond_filter) {
            $query->where('type_bond_id', $this->type_bond_filter);
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by confirmation
        if ($this->confirmation_filter) {
            $query->where('confirmation_id', $this->confirmation_filter);
        }

        // Filter by responsible
        if ($this->responsible_filter) {
            $query->where('responsible_id', $this->responsible_filter);
        }

        // Filter by induction date range
        if ($this->date_from) {
            $query->where('date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('date', '<=', $this->date_to);
        }

        // Filter by entry date range
        if ($this->entry_date_from) {
            $query->where('entry_date', '>=', $this->entry_date_from);
        }

        if ($this->entry_date_to) {
            $query->where('entry_date', '<=', $this->entry_date_to);
        }

        $inductions = $query->orderBy('created_at', 'desc')
                            ->paginate($this->perPage);

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

        return view('livewire.modules.rrhh.inductions.index', [
            'inductions' => $inductions,
            'typeBondOptions' => $typeBondOptions,
            'statusOptions' => $statusOptions,
            'confirmationOptions' => $confirmationOptions,
            'employees' => $employees,
            'responsibles' => $responsibles,
        ]);
    }
}
