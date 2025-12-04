<?php

namespace App\Livewire\Modules\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Report;
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
        $report = Report::find($id);

        if ($report) {
            $report->delete();
            session()->flash('success', 'Reporte eliminado exitosamente.');
        }
    }

    public function render()
    {
        $query = Report::with(['status', 'user']);

        // Search by Title or Description
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by date range
        if ($this->date_from) {
            $query->whereDate('date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->whereDate('date', '<=', $this->date_to);
        }

        $reports = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        // Obtener opciones para los filtros
        $statusCategory = TagCategory::where('slug', 'estado_reporte')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.reports.index', [
            'reports' => $reports,
            'statusOptions' => $statusOptions,
        ]);
    }
}
