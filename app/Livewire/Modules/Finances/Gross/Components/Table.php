<?php

namespace App\Livewire\Modules\Finances\Gross\Components;

use App\Models\Income;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $listeners = ["refresh-table" => '$refresh'];

    public function mount()
    {
    }

    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $income = Income::find($id);
        if ($income) {
            $income->delete();
            $this->dispatch('income-deleted', 'Ingreso eliminado correctamente');
        }
    }

    public function render()
    {
        $incomes = Income::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderByDesc("created_at")
            ->paginate(10);

        return view("livewire.modules.finances.gross.components.table", [
            "incomes" => $incomes,
        ]);
    }
}
