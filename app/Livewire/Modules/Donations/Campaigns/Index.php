<?php

namespace App\Livewire\Modules\Donations\Campaigns;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Campaign;
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
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'responsible_filter' => ['except' => ''],
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

    public function clearFilters()
    {
        $this->reset([
            'search',
            'status_filter',
            'responsible_filter',
            'date_from',
            'date_to',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $campaign = Campaign::find($id);

        if ($campaign) {
            $campaign->delete();
            session()->flash('success', 'Campaña eliminada exitosamente.');
        }
    }

    public function render()
    {
        $query = Campaign::with(['responsible', 'status'])
            ->withCount('donations')
            ->withSum('donations as total_donations_amount', 'amount');

        // Search by name, address, or description
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('address', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orWhere('alliances', 'like', '%' . $this->search . '%');
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by responsible
        if ($this->responsible_filter) {
            $query->where('responsible_id', $this->responsible_filter);
        }

        // Filter by date range (event date)
        if ($this->date_from) {
            $query->where('date_event', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('date_event', '<=', $this->date_to);
        }

        $campaigns = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Obtener opciones para los filtros usando TagCategory
        $statusCategory = TagCategory::where('slug', 'estado_campana')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Obtener responsables (usuarios)
        $responsibleOptions = User::orderBy('name')->get();

        return view('livewire.modules.donations.campaigns.index', [
            'campaigns' => $campaigns,
            'statusOptions' => $statusOptions,
            'responsibleOptions' => $responsibleOptions,
        ]);
    }
}
