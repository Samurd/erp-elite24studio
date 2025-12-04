<?php

namespace App\Livewire\Modules\Kpis;

use App\Models\Kpi;
use App\Models\KpiRecord;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public $kpi;
    public $search = '';
    public $date_from = '';
    public $date_to = '';
    public $perPage = 10;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
        'perPage' => ['except' => ''],
    ];

    public function mount(Kpi $kpi)
    {
        $this->kpi = $kpi;
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'date_from', 'date_to']);
        $this->resetPage();
    }

    public function delete($id)
    {
        $record = KpiRecord::findOrFail($id);
        $record->delete();
        
        session()->flash('success', 'Registro eliminado exitosamente.');
    }

    public function render()
    {
        $query = $this->kpi->records()->with(['createdBy', 'files']);

        // Aplicar filtros
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('observation', 'like', '%' . $this->search . '%')
                  ->orWhere('value', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->date_from) {
            $query->where('record_date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('record_date', '<=', $this->date_to);
        }

        $records = $query->orderBy('record_date', 'desc')->paginate($this->perPage);

        return view('livewire.modules.kpis.show', [
            'kpi' => $this->kpi,
            'records' => $records,
        ]);
    }
}
