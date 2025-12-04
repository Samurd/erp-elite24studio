<?php

namespace App\Livewire\Modules\Marketing\Cases;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CaseMarketing;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Project;
use App\Models\User;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $type_filter = '';
    public $status_filter = '';
    public $project_filter = '';
    public $responsible_filter = '';
    public $date_from = '';
    public $date_to = '';
    public $mediums_filter = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'type_filter' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'project_filter' => ['except' => ''],
        'responsible_filter' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
        'mediums_filter' => ['except' => ''],
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

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingProjectFilter()
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

    public function updatingMediumsFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'type_filter',
            'status_filter',
            'project_filter',
            'responsible_filter',
            'date_from',
            'date_to',
            'mediums_filter',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $case = CaseMarketing::find($id);
        
        if ($case) {
            $case->delete();
            session()->flash('success', 'Caso de marketing eliminado exitosamente.');
        }
    }

    public function render()
    {
        $query = CaseMarketing::with([
            'project',
            'type',
            'status',
            'responsible'
        ]);

        // Search by subject or description
        if ($this->search) {
            $query->where('subject', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        // Filter by type
        if ($this->type_filter) {
            $query->where('type_id', $this->type_filter);
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by project
        if ($this->project_filter) {
            $query->where('project_id', $this->project_filter);
        }

        // Filter by responsible
        if ($this->responsible_filter) {
            $query->where('responsible_id', $this->responsible_filter);
        }

        // Filter by mediums
        if ($this->mediums_filter) {
            $query->where('mediums', 'like', '%' . $this->mediums_filter . '%');
        }

        // Filter by date range (case date)
        if ($this->date_from) {
            $query->where('date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('date', '<=', $this->date_to);
        }

        $cases = $query->orderBy('created_at', 'desc')
                         ->paginate($this->perPage);

        // Obtener opciones para los filtros usando TagCategory
        $typeCategory = TagCategory::where('slug', 'tipo_caso_mk')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();
        
        $statusCategory = TagCategory::where('slug', 'estado_caso_mk')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        
        // Obtener proyectos
        $projects = Project::orderBy('name')->get();
        
        // Obtener usuarios para el selector de responsables
        $users = User::orderBy('name')->get();

        return view('livewire.modules.marketing.cases.index', [
            'cases' => $cases,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'users' => $users,
        ]);
    }
}
