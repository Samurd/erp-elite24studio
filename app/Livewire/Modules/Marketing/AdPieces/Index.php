<?php

namespace App\Livewire\Modules\Marketing\AdPieces;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Adpiece;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Project;
use App\Models\Team;
use App\Models\Strategy;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $type_filter = '';
    public $format_filter = '';
    public $status_filter = '';
    public $project_filter = '';
    public $team_filter = '';
    public $strategy_filter = '';
    public $media_filter = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'type_filter' => ['except' => ''],
        'format_filter' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'project_filter' => ['except' => ''],
        'team_filter' => ['except' => ''],
        'strategy_filter' => ['except' => ''],
        'media_filter' => ['except' => ''],
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

    public function updatingFormatFilter()
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

    public function updatingTeamFilter()
    {
        $this->resetPage();
    }

    public function updatingStrategyFilter()
    {
        $this->resetPage();
    }

    public function updatingMediaFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'type_filter',
            'format_filter',
            'status_filter',
            'project_filter',
            'team_filter',
            'strategy_filter',
            'media_filter',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $adpiece = Adpiece::find($id);
        
        if ($adpiece) {
            $adpiece->delete();
            session()->flash('success', 'Pieza publicitaria eliminada exitosamente.');
        }
    }

    public function render()
    {
        $query = Adpiece::with([
            'type',
            'format',
            'status',
            'project',
            'responsible',
            'strategy'
        ]);

        // Search by name or media
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('media', 'like', '%' . $this->search . '%');
        }

        // Filter by type
        if ($this->type_filter) {
            $query->where('type_id', $this->type_filter);
        }

        // Filter by format
        if ($this->format_filter) {
            $query->where('format_id', $this->format_filter);
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by project
        if ($this->project_filter) {
            $query->where('project_id', $this->project_filter);
        }

        // Filter by team
        if ($this->team_filter) {
            $query->where('team_id', $this->team_filter);
        }

        // Filter by strategy
        if ($this->strategy_filter) {
            $query->where('strategy_id', $this->strategy_filter);
        }

        // Filter by media
        if ($this->media_filter) {
            $query->where('media', 'like', '%' . $this->media_filter . '%');
        }

        $adpieces = $query->orderBy('created_at', 'desc')
                           ->paginate($this->perPage);

        // Obtener opciones para los filtros usando TagCategory
        $typeCategory = TagCategory::where('slug', 'tipo_pieza')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();
        
        $formatCategory = TagCategory::where('slug', 'formato')->first();
        $formatOptions = $formatCategory ? Tag::where('category_id', $formatCategory->id)->get() : collect();
        
        $statusCategory = TagCategory::where('slug', 'estado_pieza')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $projects = Project::all();
        $teams = \App\Services\CommonDataCacheService::getAllTeams();
        $strategies = Strategy::all();

        return view('livewire.modules.marketing.ad-pieces.index', [
            'adpieces' => $adpieces,
            'typeOptions' => $typeOptions,
            'formatOptions' => $formatOptions,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'teams' => $teams,
            'strategies' => $strategies,
        ]);
    }
}
