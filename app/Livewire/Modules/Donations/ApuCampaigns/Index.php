<?php

namespace App\Livewire\Modules\Donations\ApuCampaigns;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ApuCampaign;
use App\Models\Campaign;
use App\Models\Tag;
use App\Models\TagCategory;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $campaign_filter = '';
    public $unit_filter = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'campaign_filter' => ['except' => ''],
        'unit_filter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCampaignFilter()
    {
        $this->resetPage();
    }

    public function updatingUnitFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'campaign_filter',
            'unit_filter',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $apuCampaign = ApuCampaign::find($id);

        if ($apuCampaign) {
            $apuCampaign->delete();
            session()->flash('success', 'Registro eliminado exitosamente.');
        }
    }

    public function render()
    {
        $query = ApuCampaign::with(['campaign', 'unit']);

        // Search by description
        if ($this->search) {
            $query->where('description', 'like', '%' . $this->search . '%');
        }

        // Filter by campaign
        if ($this->campaign_filter) {
            $query->where('campaign_id', $this->campaign_filter);
        }

        // Filter by unit
        if ($this->unit_filter) {
            $query->where('unit_id', $this->unit_filter);
        }

        $apuCampaigns = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Obtener opciones para los filtros
        $campaigns = Campaign::orderBy('name')->get();

        $unitCategory = TagCategory::where('slug', 'unidad_medida')->first();
        $unitOptions = $unitCategory ? Tag::where('category_id', $unitCategory->id)->get() : collect();

        return view('livewire.modules.donations.apu-campaigns.index', [
            'apuCampaigns' => $apuCampaigns,
            'campaigns' => $campaigns,
            'unitOptions' => $unitOptions,
        ]);
    }
}
