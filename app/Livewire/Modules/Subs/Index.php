<?php

namespace App\Livewire\Modules\Subs;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sub;
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
        $sub = Sub::find($id);

        if ($sub) {
            $sub->delete();
            session()->flash('success', 'Suscripción eliminada exitosamente.');
        }
    }

    public function render()
    {
        $query = Sub::with(['status', 'frequency', 'user']);

        // Search by Name
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by date range (start_date)
        if ($this->date_from) {
            $query->whereDate('start_date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->whereDate('start_date', '<=', $this->date_to);
        }

        $subs = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Obtener opciones para los filtros
        $statusCategory = TagCategory::where('slug', 'estado_suscripcion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.subs.index', [
            'subs' => $subs,
            'statusOptions' => $statusOptions,
        ]);
    }
}
