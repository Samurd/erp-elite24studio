<?php

namespace App\Livewire\Modules\Approvals;

use App\Livewire\Forms\Modules\Approvals\Form;
use App\Models\Permission;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public Form $form;

    public $priorities;
    public $users;

    protected $listeners = [
        // 1. ESCUCHAR AL HIJO: Cuando termine de subir los archivos
        'attachments-committed' => 'finishCreation'
    ];


    public function getUsersProperty()
    {
        return \App\Services\PermissionCacheService::getUsersByArea('aprobaciones');
    }

    public function mount()
    {
        $priority_type = TagCategory::where('slug', 'tipo_prioridad')->first();

        $this->priorities = Tag::where('category_id', $priority_type->id)->get();

        $this->users = $this->getUsersProperty();
    }

    public function save(\App\Services\NotificationService $notificationService)
    {
        $approval = $this->form->store();

        foreach ($approval->approvers as $approver) {
            $notificationService->createImmediate(
                user: $approver->user,
                title: 'Nueva Solicitud de Aprobación (' . $approval->priority->name . ')',
                message: 'Se te ha asignado como aprobador en una nueva solicitud de aprobación: ' . $approval->name,
                data: [
                    'approval_name' => $approval->name,
                    'priority' => $approval->priority->name,
                    'requester' => auth()->user()->name,
                    'image_url' => public_path('images/new_approval.jpg'), // Placeholder, cambiar por imagen de aprobación si existe
                ],
                notifiable: $approval,
                sendEmail: true,
                emailTemplate: 'emails.approval-assigned'
            );
        }

        $this->dispatch('commit-attachments', [
            'id' => $approval->id,
            'name' => 'Solicitud de aprobación: ' . $approval->name
        ]);
    }

    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('approvals.index');
    }


    public function render()
    {
        return view('livewire.modules.approvals.create');
    }
}
