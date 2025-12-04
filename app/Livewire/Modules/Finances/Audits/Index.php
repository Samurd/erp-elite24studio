<?php

namespace App\Livewire\Modules\Finances\Audits;

use App\Models\Audit;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Services\AreaPermissionService;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $activeTab = '';
    public $auditTypes = [];

    public function mount()
    {
        $this->loadAuditTypes();

        // Set first available tab as active
        if (!empty($this->auditTypes)) {
            $this->activeTab = $this->auditTypes[0]['slug'];
        }
    }

    public function loadAuditTypes()
    {
        $category = TagCategory::where('slug', 'tipo_auditoria')->first();
        if (!$category)
            return;

        $allTypes = Tag::where('category_id', $category->id)->get();

        // Filter types based on permissions
        $this->auditTypes = $allTypes->filter(function ($tag) {
            // The slug in TagSeeder matches the subarea slug in AreaSeeder
            // e.g., 'financiera', 'operativa', etc.
            // We check permission for 'view' action on that area slug
            if (empty($tag->slug)) return false;
            return AreaPermissionService::canArea('view', $tag->slug);
        })->map(function ($tag) {
            return [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            ];
        })->values()->toArray();
    }

    public function setActiveTab($slug)
    {
        $this->activeTab = $slug;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $audit = Audit::findOrFail($id);
        $audit->delete();

        session()->flash('success', 'Auditoría eliminada exitosamente');
    }

    public function render()
    {
        $query = Audit::query();

        // Filter by active tab (audit type)
        if ($this->activeTab) {
            $query->whereHas('type', function ($q) {
                $q->where('slug', $this->activeTab);
            });
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('objective', 'like', '%' . $this->search . '%')
                    ->orWhere('place', 'like', '%' . $this->search . '%');
            });
        }

        $audits = $query->with(['type', 'status'])
            ->orderBy('date_audit', 'desc')
            ->paginate($this->perPage);

        return view('livewire.modules.finances.audits.index', [
            'audits' => $audits,
        ]);
    }
}
