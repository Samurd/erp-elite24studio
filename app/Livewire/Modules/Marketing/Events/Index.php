<?php

namespace App\Livewire\Modules\Marketing\Events;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Event;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $type_filter = '';
    public $status_filter = '';
    public $responsible_filter = '';
    public $date_from = '';
    public $date_to = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'type_filter' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'responsible_filter' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

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

    public function updatingResponsibleFilter()
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
            'type_filter',
            'status_filter',
            'responsible_filter',
            'date_from',
            'date_to',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $event = Event::find($id);
        
        if ($event) {
            $event->delete();
            session()->flash('success', 'Evento eliminado exitosamente.');
        }
    }

    public function render()
    {
        $query = Event::with([
            'type',
            'status',
            'responsible'
        ]);

        // Search by name or location
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%');
        }

        // Filter by type
        if ($this->type_filter) {
            $query->where('type_id', $this->type_filter);
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by responsible
        if ($this->responsible_filter) {
            $query->where('responsible_id', $this->responsible_filter);
        }

        // Filter by date range (event date)
        if ($this->date_from) {
            $query->where('event_date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('event_date', '<=', $this->date_to);
        }

        $events = $query->orderBy('event_date', 'desc')
                           ->paginate($this->perPage);

        // Obtener opciones para los filtros usando TagCategory
        $eventTypeCategory = TagCategory::where('slug', 'tipo_evento')->first();
        $eventTypeOptions = $eventTypeCategory ? Tag::where('category_id', $eventTypeCategory->id)->get() : collect();
        
        $statusCategory = TagCategory::where('slug', 'estado_evento')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        
        // Obtener usuarios para el filtro de responsable
        $responsibleOptions = User::orderBy('name')->get();

        // Calculate total of all event items
        $totalPresupuesto = $events->getCollection()->sum(function ($event) {
            return $event->items->sum('total_price');
        });

        return view('livewire.modules.marketing.events.index', [
            'events' => $events,
            'eventTypeOptions' => $eventTypeOptions,
            'statusOptions' => $statusOptions,
            'responsibleOptions' => $responsibleOptions,
            'totalPresupuesto' => $totalPresupuesto,
        ]);
    }
}
