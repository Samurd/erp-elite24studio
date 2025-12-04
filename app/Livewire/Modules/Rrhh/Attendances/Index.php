<?php

namespace App\Livewire\Modules\Rrhh\Attendances;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public $view = 'daily'; // daily or consolidated
    public $search = '';
    public $employee_filter = '';
    public $status_filter = '';
    public $modality_filter = '';
    public $date_from = '';
    public $date_to = '';
    public $month = '';
    public $year = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'view' => ['except' => 'daily'],
        'search' => ['except' => ''],
        'employee_filter' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'modality_filter' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
        'month' => ['except' => ''],
        'year' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function mount()
    {
        // Set default month and year to current
        if (empty($this->month)) {
            $this->month = date('m');
        }
        if (empty($this->year)) {
            $this->year = date('Y');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingEmployeeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingModalityFilter()
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

    public function updatingMonth()
    {
        $this->resetPage();
    }

    public function updatingYear()
    {
        $this->resetPage();
    }

    public function switchView($view)
    {
        $this->view = $view;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'employee_filter',
            'status_filter',
            'modality_filter',
            'date_from',
            'date_to',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $attendance = Attendance::find($id);

        if ($attendance) {
            $attendance->delete();
            session()->flash('success', 'Asistencia eliminada exitosamente.');
        }
    }

    public function render()
    {
        // Get daily view data
        $query = Attendance::with(['employee', 'status', 'modality']);

        // Search by employee name
        if ($this->search) {
            $query->whereHas('employee', function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by employee
        if ($this->employee_filter) {
            $query->where('employee_id', $this->employee_filter);
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by modality
        if ($this->modality_filter) {
            $query->where('modality_id', $this->modality_filter);
        }

        // Filter by date range
        if ($this->date_from) {
            $query->whereDate('date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->whereDate('date', '<=', $this->date_to);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->paginate($this->perPage);

        // Get consolidated view data
        $consolidated = $this->getConsolidatedData();

        return $this->renderView($attendances, $consolidated);
    }


    protected function getConsolidatedData()
    {
        // Get all days in the selected month/year
        $startDate = \Carbon\Carbon::create($this->year, $this->month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $consolidated = [];
        $currentDate = $startDate->copy();

        // Get total number of active employees
        $totalEmployees = Employee::count();

        // Get all status tags for estado_asistencia
        $statusCategory = TagCategory::where('slug', 'estado_asistencia')->first();
        $statusTags = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        while ($currentDate <= $endDate) {
            // Get attendances for this day
            $dayAttendances = Attendance::whereDate('date', $currentDate)->get();

            // Count by each status dynamically
            $statusCounts = [];
            foreach ($statusTags as $tag) {
                $statusCounts[$tag->id] = $dayAttendances->where('status_id', $tag->id)->count();
            }

            // Check if it's a special day (weekend or holiday)
            $specialDay = null;
            if ($currentDate->isWeekend()) {
                $specialDay = $currentDate->isSaturday() ? 'Sábado' : 'Domingo';
            }

            $consolidated[] = [
                'date' => $currentDate->copy(),
                'total_employees' => $totalEmployees,
                'status_counts' => $statusCounts,
                'special_day' => $specialDay,
            ];

            $currentDate->addDay();
        }

        return collect($consolidated);
    }

    protected function renderView($attendances = null, $consolidated = null)
    {
        // Get employees
        $employees = Employee::orderBy('full_name')->get();

        // Get status options
        $statusCategory = TagCategory::where('slug', 'estado_asistencia')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Get modality options
        $modalityCategory = TagCategory::where('slug', 'modalidad_trabajo')->first();
        $modalityOptions = $modalityCategory ? Tag::where('category_id', $modalityCategory->id)->get() : collect();

        // Year options (last 5 years and current + next)
        $yearOptions = range(date('Y') - 5, date('Y') + 1);

        // Month options
        $monthOptions = [
            '01' => 'Enero',
            '02' => 'Febrero',
            '03' => 'Marzo',
            '04' => 'Abril',
            '05' => 'Mayo',
            '06' => 'Junio',
            '07' => 'Julio',
            '08' => 'Agosto',
            '09' => 'Septiembre',
            '10' => 'Octubre',
            '11' => 'Noviembre',
            '12' => 'Diciembre',
        ];

        return view('livewire.modules.rrhh.attendances.index', [
            'attendances' => $attendances,
            'consolidated' => $consolidated,
            'employees' => $employees,
            'statusOptions' => $statusOptions,
            'statusTags' => $statusOptions, // Pass status tags for dynamic columns
            'modalityOptions' => $modalityOptions,
            'yearOptions' => $yearOptions,
            'monthOptions' => $monthOptions,
        ]);
    }
}
