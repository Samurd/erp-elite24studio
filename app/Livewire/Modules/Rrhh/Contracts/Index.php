<?php

namespace App\Livewire\Modules\Rrhh\Contracts;

use App\Models\Contract;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $statusFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $contract = Contract::findOrFail($id);
        $contract->delete();

        session()->flash('success', 'Contrato eliminado exitosamente');
    }

    public function render()
    {
        $query = Contract::query();

        if ($this->search) {
            $query->whereHas('employee', function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('identification_number', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status_id', $this->statusFilter);
        }

        $contracts = $query->with(['employee', 'type', 'category', 'status'])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $statusCategory = TagCategory::where('slug', 'estado_contrato')->first();
        $statuses = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.rrhh.contracts.index', [
            'contracts' => $contracts,
            'statuses' => $statuses,
        ]);
    }
}
