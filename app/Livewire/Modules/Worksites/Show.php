<?php

namespace App\Livewire\Modules\Worksites;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Worksite;
use App\Models\PunchItem;
use App\Models\Change;
use App\Models\Visit;
use App\Models\User;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\File;

class Show extends Component
{
    use WithPagination;

    public Worksite $worksite;
    public $search = '';
    public $status_filter = '';
    public $responsible_filter = '';
    public $perPage = 10;

    // Variables para Changes
    public $change_search = '';
    public $change_type_filter = '';
    public $change_status_filter = '';
    public $change_perPage = 10;

    // Variables para Visits
    public $visit_search = '';
    public $visit_status_filter = '';
    public $visit_visitor_filter = '';
    public $visit_perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'responsible_filter' => ['except' => ''],
        'perPage' => ['except' => 10],
        'change_search' => ['except' => ''],
        'change_type_filter' => ['except' => ''],
        'change_status_filter' => ['except' => ''],
        'change_perPage' => ['except' => 10],
        'visit_search' => ['except' => ''],
        'visit_status_filter' => ['except' => ''],
        'visit_visitor_filter' => ['except' => ''],
        'visit_perPage' => ['except' => 10],
    ];

    public function mount(Worksite $worksite)
    {
        $this->worksite = $worksite;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingResponsibleFilter()
    {
        $this->resetPage();
    }

    // Métodos para Changes
    public function updatingChangeSearch()
    {
        $this->resetPage();
    }

    public function updatingChangeTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingChangeStatusFilter()
    {
        $this->resetPage();
    }

    // Métodos para Visits
    public function updatingVisitSearch()
    {
        $this->resetPage();
    }

    public function updatingVisitStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingVisitVisitorFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'status_filter',
            'responsible_filter',
        ]);
        $this->resetPage();
    }

    public function clearChangeFilters()
    {
        $this->reset([
            'change_search',
            'change_type_filter',
            'change_status_filter',
        ]);
        $this->resetPage();
    }

    public function clearVisitFilters()
    {
        $this->reset([
            'visit_search',
            'visit_status_filter',
            'visit_visitor_filter',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $punchItem = PunchItem::find($id);
        
        if ($punchItem) {
            $punchItem->delete();
            session()->flash('success', 'Punch Item eliminado exitosamente.');
        }
    }

    public function deleteChange($id)
    {
        $change = Change::find($id);
        
        if ($change) {
            $change->delete();
            session()->flash('success', 'Cambio eliminado exitosamente.');
        }
    }

    public function deleteVisit($id)
    {
        $visit = Visit::find($id);
        
        if ($visit) {
            $visit->delete();
            session()->flash('success', 'Visita eliminada exitosamente.');
        }
    }

    public function render()
    {
        $query = PunchItem::with([
            'responsible',
            'files'
        ])->where('worksite_id', $this->worksite->id);

        // Search by observations
        if ($this->search) {
            $query->where('observations', 'like', '%' . $this->search . '%');
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by responsible
        if ($this->responsible_filter) {
            $query->where('responsible_id', $this->responsible_filter);
        }

        $punchItems = $query->orderBy('created_at', 'desc')
                           ->paginate($this->perPage);

        // Obtener usuarios para filtros
        $responsibles = User::orderBy('name')->get();

        // Obtener opciones para los estados usando TagCategory
        $statusCategory = TagCategory::where('slug', 'estado_punch_item')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Query para Changes
        $changeQuery = Change::with([
            'type',
            'status',
            'budgetImpact',
            'approver',
            'files'
        ])->where('worksite_id', $this->worksite->id);

        // Search by description
        if ($this->change_search) {
            $changeQuery->where('description', 'like', '%' . $this->change_search . '%');
        }

        // Filter by change type
        if ($this->change_type_filter) {
            $changeQuery->where('change_type_id', $this->change_type_filter);
        }

        // Filter by status
        if ($this->change_status_filter) {
            $changeQuery->where('status_id', $this->change_status_filter);
        }

        $changes = $changeQuery->orderBy('change_date', 'desc')
                              ->paginate($this->change_perPage);

        // Obtener opciones para los cambios
        $changeTypeCategory = TagCategory::where('slug', 'tipo_cambio')->first();
        $changeTypeOptions = $changeTypeCategory ? Tag::where('category_id', $changeTypeCategory->id)->get() : collect();
        
        $changeStatusCategory = TagCategory::where('slug', 'estado_cambio')->first();
        $changeStatusOptions = $changeStatusCategory ? Tag::where('category_id', $changeStatusCategory->id)->get() : collect();

        $budgetImpactCategory = TagCategory::where('slug', 'impacto_presupuesto')->first();
        $budgetImpactOptions = $budgetImpactCategory ? Tag::where('category_id', $budgetImpactCategory->id)->get() : collect();

        // Query para Visits
        $visitQuery = Visit::with([
            'visitor',
            'performer',
            'status',
            'files'
        ])->where('worksite_id', $this->worksite->id);

        // Search by notes
        if ($this->visit_search) {
            $visitQuery->where('general_observations', 'like', '%' . $this->visit_search . '%');
        }

        // Filter by status
        if ($this->visit_status_filter) {
            $visitQuery->where('status_id', $this->visit_status_filter);
        }

        // Filter by visitor
        if ($this->visit_visitor_filter) {
            $visitQuery->where('performed_by', $this->visit_visitor_filter);
        }

        $visits = $visitQuery->orderBy('visit_date', 'desc')
                             ->paginate($this->visit_perPage);

        // Obtener opciones para las visitas
        $visitTypeCategory = TagCategory::where('slug', 'tipo_visita')->first();
        $visitTypeOptions = $visitTypeCategory ? Tag::where('category_id', $visitTypeCategory->id)->get() : collect();
        
        $visitStatusCategory = TagCategory::where('slug', 'estado_visita')->first();
        $visitStatusOptions = $visitStatusCategory ? Tag::where('category_id', $visitStatusCategory->id)->get() : collect();

        // Obtener usuarios para filtros de visitas
        $visitors = User::orderBy('name')->get();

        return view('livewire.modules.worksites.show', [
            'worksite' => $this->worksite,
            'punchItems' => $punchItems,
            'responsibles' => $responsibles,
            'statusOptions' => $statusOptions,
            'changes' => $changes,
            'changeTypeOptions' => $changeTypeOptions,
            'changeStatusOptions' => $changeStatusOptions,
            'budgetImpactOptions' => $budgetImpactOptions,
            'visits' => $visits,
            'visitTypeOptions' => $visitTypeOptions,
            'visitStatusOptions' => $visitStatusOptions,
            'visitors' => $visitors,
        ]);
    }
}
