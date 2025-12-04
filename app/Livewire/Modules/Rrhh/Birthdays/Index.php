<?php

namespace App\Livewire\Modules\Rrhh\Birthdays;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Birthday;
use App\Models\Tag;
use App\Models\TagCategory;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $type_filter = '';
    public $month_filter = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'type_filter' => ['except' => ''],
        'month_filter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingMonthFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'type_filter',
            'month_filter',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $birthday = Birthday::find($id);

        if ($birthday) {
            $birthday->delete();
            session()->flash('success', 'Cumpleaños eliminado exitosamente.');
        }
    }

    public function render()
    {
        $query = Birthday::with(['employee', 'contact', 'responsible']);

        // Search by name (employee or contact)
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('employee', function ($q) {
                    $q->where('full_name', 'like', '%' . $this->search . '%');
                })->orWhereHas('contact', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })->orWhere('name', 'like', '%' . $this->search . '%'); // Fallback for legacy records
            });
        }

        // Filter by type (Simplified: Employee vs Contact)
        if ($this->type_filter) {
            if ($this->type_filter === 'employee') {
                $query->whereNotNull('employee_id');
            } elseif ($this->type_filter === 'contact') {
                $query->whereNotNull('contact_id');
            }
        }

        // Filter by month
        if ($this->month_filter) {
            $query->whereMonth('date', $this->month_filter);
        }

        $birthdays = $query->orderBy('date', 'asc')
            ->paginate($this->perPage);

        // Type options (Simplified)
        $typeOptions = collect([
            (object) ['id' => 'employee', 'name' => 'Empleado'],
            (object) ['id' => 'contact', 'name' => 'Contacto'],
        ]);

        // Month options
        $monthOptions = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        return view('livewire.modules.rrhh.birthdays.index', [
            'birthdays' => $birthdays,
            'typeOptions' => $typeOptions,
            'monthOptions' => $monthOptions,
        ]);
    }
}
