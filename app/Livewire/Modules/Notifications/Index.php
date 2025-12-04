<?php

namespace App\Livewire\Modules\Notifications;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $status_filter = ''; // 'active', 'inactive'
    public $type_filter = ''; // 'recurring', 'scheduled', 'reminder'
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = ['refresh-table' => '$refresh'];

    protected $queryString = [
        'search' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'type_filter' => ['except' => ''],
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

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'status_filter',
            'type_filter',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $template = NotificationTemplate::find($id);

        if ($template) {
            $template->delete();
            session()->flash('success', 'Notificación eliminada exitosamente.');
        }
    }

    public function toggleStatus($id)
    {
        $template = NotificationTemplate::find($id);
        if ($template) {
            $template->is_active = !$template->is_active;
            $template->save();

            $status = $template->is_active ? 'activada' : 'pausada';
            session()->flash('success', "Notificación {$status} exitosamente.");
        }
    }

    public function render()
    {
        $query = NotificationTemplate::with(['user', 'notifiable']);

        // Search by Title or Message
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('message', 'like', '%' . $this->search . '%');
            });
        }

        // Filter by Status
        if ($this->status_filter === 'active') {
            $query->active();
        } elseif ($this->status_filter === 'inactive') {
            $query->inactive();
        }

        // Filter by Type
        if ($this->type_filter) {
            $query->where('type', $this->type_filter);
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.modules.notifications.index', [
            'notifications' => $notifications,
        ]);
    }
}
