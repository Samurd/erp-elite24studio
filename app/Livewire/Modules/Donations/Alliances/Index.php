<?php

namespace App\Livewire\Modules\Donations\Alliances;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Alliance;
use App\Models\Tag;
use App\Models\TagCategory;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $type_filter = '';
    public $certified_filter = '';
    public $date_from = '';
    public $date_to = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'type_filter' => ['except' => ''],
        'certified_filter' => ['except' => ''],
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
            'type_filter',
            'certified_filter',
            'date_from',
            'date_to',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $alliance = Alliance::find($id);

        if ($alliance) {
            $alliance->delete();
            session()->flash('success', 'Alianza eliminada exitosamente.');
        }
    }

    public function render()
    {
        $query = Alliance::with(['type']);

        // Search by name
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        // Filter by type
        if ($this->type_filter) {
            $query->where('type_id', $this->type_filter);
        }

        // Filter by certified
        if ($this->certified_filter !== '') {
            $query->where('certified', $this->certified_filter);
        }

        // Filter by date range (start_date)
        if ($this->date_from) {
            $query->where('start_date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('start_date', '<=', $this->date_to);
        }

        $alliances = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Obtener opciones para los filtros
        // Asumiendo que existe una categoría 'tipo_alianza', si no, habrá que crearla o usar otra lógica
        // Por ahora usaré un placeholder o buscaré si existe algo similar en TagCategory
        $typeCategory = TagCategory::where('slug', 'tipo_alianza')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        return view('livewire.modules.donations.alliances.index', [
            'alliances' => $alliances,
            'typeOptions' => $typeOptions,
        ]);
    }
}
