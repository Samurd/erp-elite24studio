<?php

namespace App\Livewire\Modules\Finances\Expense\Components;

use App\Models\Expense;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public $listeners = ["refresh-table" => '$refresh'];

    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $expense = Expense::find($id);
        if ($expense) {
            $expense->delete();
            $this->dispatch('expense-deleted', 'Gasto eliminado correctamente');
        }
    }

    public function render()
    {
        $expenses = Expense::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderByDesc("created_at")
            ->paginate(10);

        return view("livewire.modules.finances.expense.components.table", [
            "expenses" => $expenses,
        ]);
    }
}
