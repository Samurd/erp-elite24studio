<?php

namespace App\Livewire\Modules\Rrhh\Interviews;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Interview;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use App\Models\Applicant;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $status_filter = '';
    public $interview_type_filter = '';
    public $result_filter = '';
    public $interviewer_filter = '';
    public $date_from = '';
    public $date_to = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'interview_type_filter' => ['except' => ''],
        'result_filter' => ['except' => ''],
        'interviewer_filter' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingInterviewTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingResultFilter()
    {
        $this->resetPage();
    }

    public function updatingInterviewerFilter()
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

    public function clearFilters()
    {
        $this->reset([
            'search',
            'status_filter',
            'interview_type_filter',
            'result_filter',
            'interviewer_filter',
            'date_from',
            'date_to',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $interview = Interview::find($id);
        
        if ($interview) {
            $interview->delete();
            session()->flash('success', 'Entrevista eliminada exitosamente.');
        }
    }

    public function getRrhhUsers()
    {
        // Obtener permisos del área RRHH
        $rrhhPermissionIds = \App\Models\Permission::whereHas("area", function ($query) {
            $query->where("slug", "rrhh");
        })->pluck("id");

        return User::whereHas("roles.permissions", function ($query) use ($rrhhPermissionIds) {
            $query->whereIn("permissions.id", $rrhhPermissionIds);
        })
        ->orderBy("name")
        ->get();
    }

    public function render()
    {
        $query = Interview::with([
            'applicant',
            'interviewer',
            'interviewType',
            'status',
            'result'
        ]);

        // Search by applicant name or email
        if ($this->search) {
            $query->whereHas('applicant', function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by interview type
        if ($this->interview_type_filter) {
            $query->where('interview_type_id', $this->interview_type_filter);
        }

        // Filter by result
        if ($this->result_filter) {
            $query->where('result_id', $this->result_filter);
        }

        // Filter by interviewer
        if ($this->interviewer_filter) {
            $query->where('interviewer_id', $this->interviewer_filter);
        }

        // Filter by date range
        if ($this->date_from) {
            $query->where('date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('date', '<=', $this->date_to);
        }

        $interviews = $query->orderBy('date', 'desc')
                           ->orderBy('time', 'desc')
                           ->paginate($this->perPage);

        // Obtener opciones para los filtros usando TagCategory
        $statusCategory = TagCategory::where('slug', 'estado_entrevista')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        
        $interviewTypeCategory = TagCategory::where('slug', 'tipo_entrevista')->first();
        $interviewTypeOptions = $interviewTypeCategory ? Tag::where('category_id', $interviewTypeCategory->id)->get() : collect();
        
        $resultCategory = TagCategory::where('slug', 'resultado_entrevista')->first();
        $resultOptions = $resultCategory ? Tag::where('category_id', $resultCategory->id)->get() : collect();
        
        $interviewerOptions = $this->getRrhhUsers();

        return view('livewire.modules.rrhh.interviews.index', [
            'interviews' => $interviews,
            'statusOptions' => $statusOptions,
            'interviewTypeOptions' => $interviewTypeOptions,
            'resultOptions' => $resultOptions,
            'interviewerOptions' => $interviewerOptions,
        ]);
    }
}
