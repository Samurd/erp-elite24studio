<?php

namespace App\Livewire\Modules\Marketing\Strategies;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Strategy;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $status_filter = '';
    public $responsible_filter = '';
    public $date_from = '';
    public $date_to = '';
    public $platform_filter = '';
    public $target_audience_filter = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'responsible_filter' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
        'platform_filter' => ['except' => ''],
        'target_audience_filter' => ['except' => ''],
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

    public function updatingPlatformFilter()
    {
        $this->resetPage();
    }

    public function updatingTargetAudienceFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'status_filter',
            'responsible_filter',
            'date_from',
            'date_to',
            'platform_filter',
            'target_audience_filter',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $strategy = Strategy::find($id);
        
        if ($strategy) {
            $strategy->delete();
            session()->flash('success', 'Estrategia eliminada exitosamente.');
        }
    }

    public function render()
    {
        $query = Strategy::with([
            'status',
            'responsible'
        ]);

        // Search by name, objective, or observations
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('objective', 'like', '%' . $this->search . '%')
                  ->orWhere('observations', 'like', '%' . $this->search . '%');
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by responsible
        if ($this->responsible_filter) {
            $query->where('responsible_id', $this->responsible_filter);
        }

        // Filter by platform
        if ($this->platform_filter) {
            $query->where('platforms', 'like', '%' . $this->platform_filter . '%');
        }

        // Filter by target audience
        if ($this->target_audience_filter) {
            $query->where('target_audience', 'like', '%' . $this->target_audience_filter . '%');
        }

        // Filter by date range (start date)
        if ($this->date_from) {
            $query->where('start_date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('end_date', '<=', $this->date_to);
        }

        $strategies = $query->orderBy('created_at', 'desc')
                           ->paginate($this->perPage);

        // Obtener opciones para los filtros usando TagCategory
        $statusCategory = TagCategory::where('slug', 'estado_estrategia')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        
        // Obtener usuarios para el filtro de responsables
        $responsibleOptions = User::orderBy('name')->get();

        return view('livewire.modules.marketing.strategies.index', [
            'strategies' => $strategies,
            'statusOptions' => $statusOptions,
            'responsibleOptions' => $responsibleOptions,
        ]);
    }
}
