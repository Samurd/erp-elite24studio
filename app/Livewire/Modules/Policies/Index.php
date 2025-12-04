<?php

namespace App\Livewire\Modules\Policies;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Policy;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $type_filter = '';
    public $status_filter = '';
    public $assigned_to_filter = '';
    public $date_from = '';
    public $date_to = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'type_filter' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'assigned_to_filter' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
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

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingAssignedToFilter()
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
            'type_filter',
            'status_filter',
            'assigned_to_filter',
            'date_from',
            'date_to',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $policy = Policy::find($id);
        
        if ($policy) {
            $policy->delete();
            session()->flash('success', 'Política eliminada exitosamente.');
        }
    }

    public function render()
    {
        $query = Policy::with([
            'type',
            'status',
            'assignedTo',
            'files'
        ]);

        // Search by name or description
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        // Filter by type
        if ($this->type_filter) {
            $query->where('type_id', $this->type_filter);
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by assigned user
        if ($this->assigned_to_filter) {
            $query->where('assigned_to_id', $this->assigned_to_filter);
        }

        // Filter by date range (issued date)
        if ($this->date_from) {
            $query->where('issued_at', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('issued_at', '<=', $this->date_to);
        }

        $policies = $query->orderBy('created_at', 'desc')
                           ->paginate($this->perPage);

        // Obtener opciones para los filtros usando TagCategory
        $typeCategory = TagCategory::where('slug', 'tipo_politica')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();
        
        $statusCategory = TagCategory::where('slug', 'estado_politica')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Obtener usuarios para asignación
        $userOptions = User::all();

        return view('livewire.modules.policies.index', [
            'policies' => $policies,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'userOptions' => $userOptions,
        ]);
    }
}
