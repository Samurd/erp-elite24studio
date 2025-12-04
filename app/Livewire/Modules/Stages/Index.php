<?php

namespace App\Livewire\Modules\Stages;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Stage;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $stage = Stage::find($id);
        
        if ($stage) {
            $stage->delete();
            session()->flash('success', 'Etapa eliminada exitosamente.');
        }
    }

    public function render()
    {
        $query = Stage::query();

        // Search by name or description
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        $stages = $query->orderBy('name', 'asc')
                           ->paginate($this->perPage);

        return view('livewire.modules.stages.index', [
            'stages' => $stages,
        ]);
    }
}