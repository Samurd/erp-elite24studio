<?php

namespace App\Livewire\Modules\Projects;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Contact;
use App\Models\User;
use App\Models\Stage;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $status_filter = '';
    public $project_type_filter = '';
    public $contact_filter = '';
    public $responsible_filter = '';
    public $current_stage_filter = '';
    public $date_from = '';
    public $date_to = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'project_type_filter' => ['except' => ''],
        'contact_filter' => ['except' => ''],
        'responsible_filter' => ['except' => ''],
        'current_stage_filter' => ['except' => ''],
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

    public function updatingProjectTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingContactFilter()
    {
        $this->resetPage();
    }

    public function updatingResponsibleFilter()
    {
        $this->resetPage();
    }

    public function updatingCurrentStageFilter()
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
            'project_type_filter',
            'contact_filter',
            'responsible_filter',
            'current_stage_filter',
            'date_from',
            'date_to',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $project = Project::find($id);
        
        if ($project) {
            $project->delete();
            session()->flash('success', 'Proyecto eliminado exitosamente.');
        }
    }

    public function render()
    {
        $query = Project::with([
            'contact',
            'status',
            'projectType',
            'currentStage',
            'responsible'
        ]);

        // Search by name, description, or direction
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('direction', 'like', '%' . $this->search . '%');
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by project type
        if ($this->project_type_filter) {
            $query->where('project_type_id', $this->project_type_filter);
        }

        // Filter by contact
        if ($this->contact_filter) {
            $query->where('contact_id', $this->contact_filter);
        }

        // Filter by responsible
        if ($this->responsible_filter) {
            $query->where('responsible_id', $this->responsible_filter);
        }

        // Filter by current stage
        if ($this->current_stage_filter) {
            $query->where('current_stage_id', $this->current_stage_filter);
        }

        // Filter by date range (created_at)
        if ($this->date_from) {
            $query->where('created_at', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('created_at', '<=', $this->date_to);
        }

        $projects = $query->orderBy('created_at', 'desc')
                           ->paginate($this->perPage);

        // Obtener opciones para los filtros usando TagCategory
        $statusCategory = TagCategory::where('slug', 'estado_proyecto')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        
        $projectTypeCategory = TagCategory::where('slug', 'tipo_proyecto')->first();
        $projectTypeOptions = $projectTypeCategory ? Tag::where('category_id', $projectTypeCategory->id)->get() : collect();

        // Obtener contactos
        $contactOptions = Contact::all();

        // Obtener usuarios responsables
        $responsibleOptions = User::all();

        // Obtener etapas
        $stageOptions = Stage::all();

        return view('livewire.modules.projects.index', [
            'projects' => $projects,
            'statusOptions' => $statusOptions,
            'projectTypeOptions' => $projectTypeOptions,
            'contactOptions' => $contactOptions,
            'responsibleOptions' => $responsibleOptions,
            'stageOptions' => $stageOptions,
        ]);
    }
}
