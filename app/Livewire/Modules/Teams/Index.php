<?php

namespace App\Livewire\Modules\Teams;

use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $isPublicFilter = '';

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'isPublicFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatingIsPublicFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->perPage = 10;
        $this->isPublicFilter = '';
        $this->resetPage();
    }

    public function render()
    {
        $currentUserId = Auth::id();

        $teamsQuery = Team::with([
            'members' => function ($query) use ($currentUserId) {
                $query->where('user_id', $currentUserId);
            },
            'channels'
        ])
            ->withCount(['members', 'channels']);

        // Aplicar filtro de búsqueda
        if ($this->search) {
            $teamsQuery->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        // Aplicar filtro de público/privado
        if ($this->isPublicFilter !== '') {
            $teamsQuery->where('isPublic', $this->isPublicFilter);
        }

        $teams = $teamsQuery->paginate($this->perPage);

        return view('livewire.modules.teams.index', compact('teams'));
    }
}
