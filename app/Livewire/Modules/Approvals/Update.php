<?php

namespace App\Livewire\Modules\Approvals;

use App\Livewire\Forms\Modules\Approvals\Form;
use App\Models\Approval;
use App\Models\Permission;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class Update extends Component
{
    use WithFileUploads;

    public Form $form;
    public Approval $approval;

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

    public function mount(Approval $approval)
    {
        $this->approval = $approval;
        $this->form->setApproval($approval);

        $priority_type = TagCategory::where('slug', 'tipo_prioridad')->first();
        $this->priorities = Tag::where('category_id', $priority_type->id)->get();
        $this->users = $this->getUsersProperty();
    }

    public function save()
    {
        $this->form->update();

        return redirect()->route('approvals.index')->with('message', 'Solicitud actualizada correctamente.');
    }

    public function removeTempFile($index)
    {
        $this->form->removeTempFile($index);
    }

    public function cleanupTempFiles()
    {
        $this->form->cleanupTempFiles();
    }

    public function render()
    {
        return view('livewire.modules.approvals.create');
    }
}
