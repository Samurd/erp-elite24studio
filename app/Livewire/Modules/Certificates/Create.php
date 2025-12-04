<?php

namespace App\Livewire\Modules\Certificates;

use App\Livewire\Forms\Modules\Certificates\Form;
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

    public $isEdit = false;

    public $users;

    public $certificate_types;

    public $states;

    protected $listeners = [
        'attachments-committed' => 'finishCreation'

    ];


    public function mount()
    {
        $cert_type = TagCategory::where('slug', 'tipo_certificado')->first();
        $state_type = TagCategory::where('slug', 'estado_certificado')->first();

        $this->certificate_types = Tag::where('category_id', $cert_type?->id)->get() ?? [];
        $this->states = Tag::where('category_id', $state_type?->id)->get() ?? [];

        $this->users = $this->getUsersProperty();

        $this->form->issued_at = now()->format('Y-m-d');
        $this->form->expires_at = now()->format('Y-m-d');
    }


    public function getUsersProperty()
    {
        $policyAreaPermissionIds = Permission::whereHas('area', function ($query) {
            $query->where('slug', 'certificados');
        })->pluck('id');

        return User::whereHas('roles.permissions', function ($query) use ($policyAreaPermissionIds) {
            $query->whereIn('permissions.id', $policyAreaPermissionIds);
        })
            ->orderBy('name')
            ->get();
    }

    public function save()
    {

        $certificate = $this->form->store();

        $this->dispatch('commit-attachments', [
            'id' => $certificate->id,
            'name' => $certificate->name
        ]);
    }

    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('certificates.index');
    }
    public function render()
    {
        return view('livewire.modules.certificates.create');
    }
}
