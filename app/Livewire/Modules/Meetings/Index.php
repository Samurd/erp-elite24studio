<?php

namespace App\Livewire\Modules\Meetings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Meeting;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Team;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $status_filter = '';
    public $team_filter = '';
    public $goal_filter = '';
    public $date_from = '';
    public $date_to = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'team_filter' => ['except' => ''],
        'goal_filter' => ['except' => ''],
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

    public function updatingTeamFilter()
    {
        $this->resetPage();
    }

    public function updatingGoalFilter()
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
            'team_filter',
            'goal_filter',
            'date_from',
            'date_to',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $meeting = Meeting::find($id);
        
        if ($meeting) {
            $meeting->delete();
            session()->flash('success', 'Reunión eliminada exitosamente.');
        }
    }

    public function render()
    {
        $query = Meeting::with([
            'team',
            'status',
            'responsibles'
        ]);

        // Search by title or notes
        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('notes', 'like', '%' . $this->search . '%')
                  ->orWhere('observations', 'like', '%' . $this->search . '%');
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by team
        if ($this->team_filter) {
            $query->where('team_id', $this->team_filter);
        }

        // Filter by goal
        if ($this->goal_filter !== '') {
            $query->where('goal', $this->goal_filter);
        }

        // Filter by date range
        if ($this->date_from) {
            $query->where('date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('date', '<=', $this->date_to);
        }

        $meetings = $query->orderBy('date', 'desc')
                           ->orderBy('start_time', 'desc')
                           ->paginate($this->perPage);

        // Obtener opciones para los filtros
        $statusCategory = TagCategory::where('slug', 'estado_reunion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        
        $teamOptions = Team::all();

        return view('livewire.modules.meetings.index', [
            'meetings' => $meetings,
            'statusOptions' => $statusOptions,
            'teamOptions' => $teamOptions,
        ]);
    }
}
