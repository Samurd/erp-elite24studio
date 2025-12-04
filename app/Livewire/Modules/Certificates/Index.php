<?php

namespace App\Livewire\Modules\Certificates;

use App\Models\Certificate;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

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

    #[On('refresh-index')]
    public function refreshData()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $certificate = Certificate::find($id);
        
        if ($certificate) {
            $certificate->delete();
            session()->flash('success', 'Certificado eliminado exitosamente.');
        }
    }

    public function render()
    {
        $query = Certificate::with([
            'type',
            'status',
            'assignedTo'
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

        // Filter by date range (issued_at)
        if ($this->date_from) {
            $query->where('issued_at', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('issued_at', '<=', $this->date_to);
        }

        $certificates = $query->orderBy('created_at', 'desc')
                             ->paginate($this->perPage);

        // Obtener opciones para los filtros usando TagCategory
        $typeCategory = TagCategory::where('slug', 'tipo_certificado')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();
        
        $statusCategory = TagCategory::where('slug', 'estado_certificado')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Obtener usuarios para el filtro de asignado
        $userOptions = User::orderBy('name')->get();

        return view('livewire.modules.certificates.index', [
            'certificates' => $certificates,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'userOptions' => $userOptions,
        ]);
    }
}
