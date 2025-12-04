<?php

namespace App\Livewire\Modules\Donations\Volunteers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Volunteer;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Campaign;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $campaign_filter = '';
    public $status_filter = '';
    public $role_filter = '';
    public $certified_filter = '';
    public $city_filter = '';
    public $country_filter = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'campaign_filter' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'role_filter' => ['except' => ''],
        'certified_filter' => ['except' => ''],
        'city_filter' => ['except' => ''],
        'country_filter' => ['except' => ''],
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

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingCertifiedFilter()
    {
        $this->resetPage();
    }

    public function updatingCityFilter()
    {
        $this->resetPage();
    }

    public function updatingCountryFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'campaign_filter',
            'status_filter',
            'role_filter',
            'certified_filter',
            'city_filter',
            'country_filter',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $volunteer = Volunteer::find($id);
        
        if ($volunteer) {
            $volunteer->delete();
            session()->flash('success', 'Voluntario eliminado exitosamente.');
        }
    }

    public function render()
    {
        $query = Volunteer::with([
            'campaign',
            'status'
        ]);

        // Search by name, email, or phone
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
        }

        // Filter by campaign
        if ($this->campaign_filter) {
            $query->where('campaign_id', $this->campaign_filter);
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by role
        if ($this->role_filter) {
            $query->where('role', 'like', '%' . $this->role_filter . '%');
        }

        // Filter by certified
        if ($this->certified_filter !== '') {
            $query->where('certified', $this->certified_filter);
        }

        // Filter by city
        if ($this->city_filter) {
            $query->where('city', 'like', '%' . $this->city_filter . '%');
        }

        // Filter by country
        if ($this->country_filter) {
            $query->where('country', 'like', '%' . $this->country_filter . '%');
        }

        $volunteers = $query->orderBy('created_at', 'desc')
                           ->paginate($this->perPage);

        // Obtener opciones para los filtros
        $campaigns = Campaign::all();
        
        $statusCategory = TagCategory::where('slug', 'estado_voluntario')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.donations.volunteers.index', [
            'volunteers' => $volunteers,
            'campaigns' => $campaigns,
            'statusOptions' => $statusOptions,
        ]);
    }
}
