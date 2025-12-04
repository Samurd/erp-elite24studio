<?php

namespace App\Livewire\Modules\Quotes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Quote;
use App\Models\Tag;
use App\Models\TagCategory;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $status_filter = '';
    public $date_from = '';
    public $date_to = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'status_filter' => ['except' => ''],
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
            'date_from',
            'date_to',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $quote = Quote::find($id);

        if ($quote) {
            $quote->delete();
            session()->flash('success', 'Cotización eliminada exitosamente.');
        }
    }

    public function render()
    {
        $query = Quote::with(['contact', 'status', 'user']);

        // Search by ID or Contact Name
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('contact', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by date range
        if ($this->date_from) {
            $query->whereDate('issued_at', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->whereDate('issued_at', '<=', $this->date_to);
        }

        $quotes = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Obtener opciones para los filtros
        $statusCategory = TagCategory::where('slug', 'estado_cotizacion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.quotes.index', [
            'quotes' => $quotes,
            'statusOptions' => $statusOptions,
        ]);
    }
}
