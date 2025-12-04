<?php

namespace App\Livewire\Modules\Marketing\SocialMediaPost;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SocialMediaPost;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Project;
use App\Models\User;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $mediums_filter = '';
    public $content_type_filter = '';
    public $status_filter = '';
    public $project_filter = '';
    public $responsible_filter = '';
    public $date_from = '';
    public $date_to = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'mediums_filter' => ['except' => ''],
        'content_type_filter' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'project_filter' => ['except' => ''],
        'responsible_filter' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingMediumsFilter()
    {
        $this->resetPage();
    }

    public function updatingContentTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingProjectFilter()
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
            'mediums_filter',
            'content_type_filter',
            'status_filter',
            'project_filter',
            'responsible_filter',
            'date_from',
            'date_to',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $post = SocialMediaPost::find($id);
        
        if ($post) {
            $post->delete();
            session()->flash('success', 'Publicación eliminada exitosamente.');
        }
    }

    public function render()
    {
        $query = SocialMediaPost::with([
            'project',
            'status',
            'responsible'
        ]);

        // Search by piece name or comments
        if ($this->search) {
            $query->where('piece_name', 'like', '%' . $this->search . '%')
                  ->orWhere('comments', 'like', '%' . $this->search . '%');
        }

        // Filter by mediums
        if ($this->mediums_filter) {
            $query->where('mediums', 'like', '%' . $this->mediums_filter . '%');
        }

        // Filter by content type
        if ($this->content_type_filter) {
            $query->where('content_type', 'like', '%' . $this->content_type_filter . '%');
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by project
        if ($this->project_filter) {
            $query->where('project_id', $this->project_filter);
        }

        // Filter by responsible
        if ($this->responsible_filter) {
            $query->where('responsible_id', $this->responsible_filter);
        }

        // Filter by date range (scheduled date)
        if ($this->date_from) {
            $query->where('scheduled_date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('scheduled_date', '<=', $this->date_to);
        }

        $posts = $query->orderBy('created_at', 'desc')
                      ->paginate($this->perPage);

        // Obtener opciones para los filtros usando TagCategory
        $statusCategory = TagCategory::where('slug', 'estado_publicacion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        
        // Obtener proyectos
        $projects = Project::orderBy('name')->get();
        
        // Obtener usuarios para el filtro de responsables
        $users = User::orderBy('name')->get();

        return view('livewire.modules.marketing.social-media-post.index', [
            'posts' => $posts,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'users' => $users,
        ]);
    }
}
