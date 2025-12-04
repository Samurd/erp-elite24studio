<?php

namespace App\Livewire\Modules\Donations\Donations;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Donation;
use App\Models\Campaign;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $campaign_filter = '';
    public $payment_method_filter = '';
    public $certified_filter = '';
    public $date_from = '';
    public $date_to = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'campaign_filter' => ['except' => ''],
        'payment_method_filter' => ['except' => ''],
        'certified_filter' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
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

    public function updatingPaymentMethodFilter()
    {
        $this->resetPage();
    }

    public function updatingCertifiedFilter()
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
            'campaign_filter',
            'payment_method_filter',
            'certified_filter',
            'date_from',
            'date_to',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $donation = Donation::find($id);
        
        if ($donation) {
            $donation->delete();
            session()->flash('success', 'Donación eliminada exitosamente.');
        }
    }

    public function render()
    {
        $query = Donation::with(['campaign']);

        // Search by donor name
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Filter by campaign
        if ($this->campaign_filter) {
            $query->where('campaign_id', $this->campaign_filter);
        }

        // Filter by payment method
        if ($this->payment_method_filter) {
            $query->where('payment_method', 'like', '%' . $this->payment_method_filter . '%');
        }

        // Filter by certified status
        if ($this->certified_filter !== '') {
            $query->where('certified', $this->certified_filter);
        }

        // Filter by date range (donation date)
        if ($this->date_from) {
            $query->where('date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('date', '<=', $this->date_to);
        }

        $donations = $query->orderBy('created_at', 'desc')
                           ->paginate($this->perPage);

        // Obtener opciones para los filtros
        $campaigns = Campaign::orderBy('name')->get();
        
        $paymentMethods = Donation::distinct()->pluck('payment_method')->filter()->sort()->values();

        return view('livewire.modules.donations.donations.index', [
            'donations' => $donations,
            'campaigns' => $campaigns,
            'paymentMethods' => $paymentMethods,
        ]);
    }
}
