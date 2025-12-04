<?php

namespace App\Livewire\Modules\Rrhh\OffBoardings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\OffBoarding;
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

    protected $paginationTheme = 'bootstrap';

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

    public function clearFilters()
    {
        $this->reset([
            'search',
            'status_filter',
            'date_from',
            'date_to',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $offBoarding = OffBoarding::find($id);

        if ($offBoarding) {
            $offBoarding->delete();
            session()->flash('success', 'OffBoarding eliminado exitosamente.');
        }
    }

    public function render()
    {
        $query = OffBoarding::with(['employee', 'project', 'status', 'responsible', 'tasks']);

        // Search by employee name or project name
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('employee', function ($eq) {
                    $eq->where('full_name', 'like', '%' . $this->search . '%');
                })
                    ->orWhereHas('project', function ($pq) {
                        $pq->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('reason', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by exit date range
        if ($this->date_from) {
            $query->whereDate('exit_date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->whereDate('exit_date', '<=', $this->date_to);
        }

        $offBoardings = $query->orderBy('exit_date', 'desc')
            ->paginate($this->perPage);

        // Get status options
        $statusCategory = TagCategory::where('slug', 'estado_offboarding')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.rrhh.off-boardings.index', [
            'offBoardings' => $offBoardings,
            'statusOptions' => $statusOptions,
        ]);
    }
}
