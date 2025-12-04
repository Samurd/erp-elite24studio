<?php

namespace App\Livewire\Modules\Approvals;

use App\Livewire\Forms\Modules\Approvals\Form;
use App\Models\Approval;
use App\Models\Area;
use App\Models\Permission;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{

    use WithFileUploads, WithPagination;

    public $perPage = 6; // amount of object by page
    protected $paginationTheme = 'tailwind';

    protected $listeners = ['refresh' => '$refresh'];

    public $comment;

    public Form $form;
    // Modals
    // public $showModal = false; // Removed
    public $isDeleteModalOpen = false;

    public $selectedApproval = null;

    public $createModalOpen = false;
    public $hasResponded = false;

    public $states;

    public $priorities;
    public $users;

    public function getUsersProperty()
    {
        $approvalAreaPermissionIds = Permission::whereHas('area', function ($query) {
            $query->where('slug', 'aprobaciones');
        })->pluck('id');

        return User::whereHas('roles.permissions', function ($query) use ($approvalAreaPermissionIds) {
            $query->whereIn('permissions.id', $approvalAreaPermissionIds);
        })
            ->orderBy('name')
            ->get();
    }


    public function mount()
    {
        $priority_type = TagCategory::where('slug', 'tipo_prioridad')->first();

        $this->priorities = Tag::where('category_id', $priority_type->id)->get();

        $this->users = $this->getUsersProperty();
    }

    public function showApproval($id)
    {

        $state_type = TagCategory::where('slug', 'estado_aprobacion')->first();

        $statePending = Tag::where('category_id', $state_type->id)->where('name', "En espera")->first();
        $this->selectedApproval = Approval::with(['creator', 'approvers', 'files', 'status'])->find($id);

        $approver = $this->selectedApproval->approvers()
            ->where('user_id', Auth::user()->id)
            ->first();

        $this->hasResponded = $approver && $approver->status && $approver->status->id == $statePending->id;
        // $this->showModal = true; // Removed
    }

    public function save()
    {
        $this->form->store();

        $this->closeCreateModal();

        $this->resetPage();
    }


    public function updatedCreateModalOpen($value)
    {
        if (!$value) {
            $this->form->reset();
        }
    }

    public function approve($approvalId)
    {
        $user = Auth::user();
        $approval = Approval::with('approvers')->findOrFail($approvalId);

        $approver = $approval->approvers->where('user_id', $user->id)->first();

        if (!$approver) {
            session()->flash('error', 'No estás autorizado para aprobar esta solicitud.');
            return;
        }

        $statusApproved = Tag::whereHas('category', fn($q) => $q->where('slug', 'estado_aprobacion'))
            ->where('name', 'Aprobado')->first();

        $statusRejected = Tag::whereHas('category', fn($q) => $q->where('slug', 'estado_aprobacion'))
            ->where('name', 'Rechazado')->first();

        $approver->update([
            'status_id' => $statusApproved->id,
            'comment' => $this->comment,
            'responded_at' => now(),
        ]);

        // 🔹 Si se requiere la respuesta de todos los aprobadores
        if ($approval->all_approvers) {
            // Si alguien rechazó, el approval se marca como rechazado
            if ($approval->approvers->contains(fn($a) => $a->status_id === $statusRejected->id)) {
                $approval->update([
                    'status_id' => $statusRejected->id,
                    'rejected_at' => now(),
                ]);
            }
            // Si todos aprobaron, el approval se marca como aprobado
            elseif ($approval->approvers->every(fn($a) => $a->status_id === $statusApproved->id)) {
                $approval->update([
                    'status_id' => $statusApproved->id,
                    'approved_at' => now(),
                ]);
            }
            // Si aún faltan respuestas, se mantiene en espera
        } else {
            // 🔹 Si no se requiere la respuesta de todos, la primera decisión define el resultado
            $approval->update([
                'status_id' => $statusApproved->id,
                'approved_at' => now(),
            ]);
        }

        // $this->showModal = false; // Removed
        $this->dispatch('close-approval-modal'); // Added
        $this->comment = null;
        $this->dispatch('refresh');
        session()->flash('message', 'Solicitud aprobada correctamente.');
    }

    public function reject($approvalId)
    {
        $user = Auth::user();
        $approval = Approval::with('approvers')->findOrFail($approvalId);

        $approver = $approval->approvers->where('user_id', $user->id)->first();

        if (!$approver) {
            session()->flash('error', 'No estás autorizado para rechazar esta solicitud.');
            return;
        }

        $statusRejected = Tag::whereHas('category', fn($q) => $q->where('slug', 'estado_aprobacion'))
            ->where('name', 'Rechazado')->first();

        $statusApproved = Tag::whereHas('category', fn($q) => $q->where('slug', 'estado_aprobacion'))
            ->where('name', 'Aprobado')->first();

        $approver->update([
            'status_id' => $statusRejected->id,
            'comment' => $this->comment,
            'responded_at' => now(),
        ]);

        // 🔹 Si requiere aprobación de todos
        if ($approval->all_approvers) {
            // Si alguien rechaza, el approval se marca como rechazado inmediatamente
            $approval->update([
                'status_id' => $statusRejected->id,
                'rejected_at' => now(),
            ]);
        } else {
            // 🔹 Si no requiere todos, también se marca rechazado de inmediato
            $approval->update([
                'status_id' => $statusRejected->id,
                'rejected_at' => now(),
            ]);
        }

        // $this->showModal = false; // Removed
        $this->dispatch('close-approval-modal'); // Added
        $this->comment = null;
        $this->dispatch('refresh');
        session()->flash('message', 'Solicitud rechazada correctamente.');
    }


    public function deleteApproval($id)
    {
        $approval = Approval::find($id);

        if ($approval) {
            $approval->delete();
            session()->flash('message', 'Solicitud eliminada correctamente.');
        }

        $this->dispatch('refresh');
    }

    public function openCreateModal()
    {
        $this->createModalOpen = true;
    }
    public function closeCreateModal()
    {
        $this->createModalOpen = false;
    }




    public function render()
    {
        $current_user = Auth::user();

        $received_approvals = Approval::where('buy', false)->whereHas('approvers', function ($query) use ($current_user) {
            $query->where('user_id', $current_user->id);
        })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);
        $received_buy_approvals = Approval::where('buy', true)->whereHas('approvers', function ($query) use ($current_user) {
            $query->where('user_id', $current_user->id);
        })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $approvals_sent = Approval::where('buy', false)
            ->where('created_by_id', $current_user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $buy_approvals_sent = Approval::where('buy', true)
            ->where('created_by_id', $current_user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.modules.approvals.index', [
            'received_approvals' => $received_approvals,
            'received_buy_approvals' => $received_buy_approvals,
            'approvals_sent' => $approvals_sent,
            'buy_approvals_sent' => $buy_approvals_sent,
        ]);
    }
}
