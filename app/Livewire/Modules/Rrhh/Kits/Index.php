<?php

namespace App\Livewire\Modules\Rrhh\Kits;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kit;
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
        $kit = Kit::find($id);

        if ($kit) {
            $kit->delete();
            session()->flash('success', 'Kit eliminado exitosamente.');
        }
    }

    public function render()
    {
        $query = Kit::with(['requestedByUser', 'status', 'deliveryResponsibleUser']);

        // Search by recipient name, role, or position
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('recipient_name', 'like', '%' . $this->search . '%')
                    ->orWhere('recipient_role', 'like', '%' . $this->search . '%')
                    ->orWhere('position_area', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by request date range
        if ($this->date_from) {
            $query->whereDate('request_date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->whereDate('request_date', '<=', $this->date_to);
        }

        $kits = $query->orderBy('request_date', 'desc')
            ->paginate($this->perPage);

        // Get status options
        $statusCategory = TagCategory::where('slug', 'estado_kit')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.rrhh.kits.index', [
            'kits' => $kits,
            'statusOptions' => $statusOptions,
        ]);
    }
}
