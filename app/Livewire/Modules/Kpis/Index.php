<?php

namespace App\Livewire\Modules\Kpis;

use App\Models\Kpi;
use App\Models\KpiRecord;
use App\Models\Role;
use App\Models\User;
use Livewire\Component;

class Index extends Component
{
    public $search = '';
    public $role_filter = '';
    public $period_filter = '';
    public $perPage = 10;
    
    protected $queryString = [
        'search' => ['except' => ''],
        'role_filter' => ['except' => ''],
        'period_filter' => ['except' => ''],
        'perPage' => ['except' => ''],
    ];

    public function mount()
    {
        $this->perPage = session()->get('perPage', 10);
    }

    public function updatedPerPage()
    {
        session()->put('perPage', $this->perPage);
    }

    public function clearFilters()
    {
        $this->reset(['search', 'role_filter', 'period_filter']);
    }

    public function render()
    {
        $query = Kpi::with(['role', 'records' => function ($query) {
            $query->latest('record_date');
        }]);

        // Aplicar filtros
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('indicator_name', 'like', '%' . $this->search . '%')
                  ->orWhere('protocol_code', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->role_filter) {
            $query->where('role_id', $this->role_filter);
        }

        if ($this->period_filter) {
            if ($this->period_filter === 'week') {
                $query->whereHas('records', function ($q) {
                    $q->where('record_date', '>=', now()->subDays(7));
                });
            } elseif ($this->period_filter === 'month') {
                $query->whereHas('records', function ($q) {
                    $q->where('record_date', '>=', now()->subDays(30));
                });
            } elseif ($this->period_filter === 'quarter') {
                $query->whereHas('records', function ($q) {
                    $q->where('record_date', '>=', now()->subDays(90));
                });
            }
        }

        $kpis = $query->paginate($this->perPage);

        // Obtener opciones para filtros
        $roles = Role::orderBy('name')->get();

        return view('livewire.modules.kpis.index', [
            'kpis' => $kpis,
            'roles' => $roles,
        ]);
    }

    public function delete($id)
    {
        $kpi = Kpi::findOrFail($id);
        $kpi->delete();
        
        session()->flash('success', 'KPI eliminado exitosamente.');
        
        return redirect()->route('kpis.index');
    }
}
