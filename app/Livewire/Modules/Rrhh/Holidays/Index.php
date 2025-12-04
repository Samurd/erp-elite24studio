<?php

namespace App\Livewire\Modules\Rrhh\Holidays;

use App\Models\Holiday;
use App\Models\Tag;
use App\Models\TagCategory;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $type_filter = '';
    public $status_filter = '';
    public $year = '';
    public $perPage = 10;

    public function mount()
    {
        $this->year = date('Y');
    }

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

    public function updatingYear()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->type_filter = '';
        $this->status_filter = '';
        $this->year = date('Y');
        $this->resetPage();
    }

    public function delete($id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();

        session()->flash('success', 'Festivo eliminado exitosamente');
        $this->resetPage();
    }

    public function render()
    {
        $query = Holiday::with(['employee', 'type', 'status', 'approver']);

        // Search by employee name or ID
        if ($this->search) {
            $query->whereHas('employee', function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('identification_number', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by type
        if ($this->type_filter) {
            $query->where('type_id', $this->type_filter);
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by year
        if ($this->year) {
            $query->whereYear('start_date', $this->year);
        }

        $holidays = $query->orderBy('start_date', 'desc')->paginate($this->perPage);

        // Get filter options
        $typeCategory = TagCategory::where('slug', 'tipo_festivo')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_festivo')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Year options (last 5 years and current + next 2)
        $yearOptions = range(date('Y') - 5, date('Y') + 2);

        return view('livewire.modules.rrhh.holidays.index', [
            'holidays' => $holidays,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'yearOptions' => $yearOptions,
        ]);
    }
}
