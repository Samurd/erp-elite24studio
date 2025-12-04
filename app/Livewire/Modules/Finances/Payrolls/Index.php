<?php

namespace App\Livewire\Modules\Finances\Payrolls;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Payroll;
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

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status_filter = '';
        $this->date_from = '';
        $this->date_to = '';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function delete($id)
    {
        $payroll = Payroll::find($id);
        if ($payroll) {
            $payroll->delete();
            session()->flash('success', 'Nómina eliminada exitosamente.');
        }
    }

    public function render()
    {
        $query = Payroll::query()->with(['employee', 'status']);

        if ($this->search) {
            $query->whereHas('employee', function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        if ($this->date_from) {
            $query->whereDate('created_at', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->whereDate('created_at', '<=', $this->date_to);
        }

        $payrolls = $query->latest()->paginate($this->perPage);

        $statusCategory = TagCategory::where('slug', 'estado_nomina')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.finances.payrolls.index', [
            'payrolls' => $payrolls,
            'statusOptions' => $statusOptions,
        ]);
    }
}
