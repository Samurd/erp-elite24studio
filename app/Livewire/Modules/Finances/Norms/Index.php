<?php

namespace App\Livewire\Modules\Finances\Norms;

use App\Models\Norm;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $norm = Norm::findOrFail($id);
        $norm->delete();

        session()->flash('success', 'Norma eliminada exitosamente');
    }

    public function render()
    {
        $query = Norm::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $norms = $query->orderBy('created_at', 'desc')->paginate($this->perPage);

        return view('livewire.modules.finances.norms.index', [
            'norms' => $norms,
        ]);
    }
}
