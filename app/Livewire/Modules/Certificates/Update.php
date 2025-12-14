<?php

namespace App\Livewire\Modules\Certificates;

use App\Livewire\Forms\Modules\Certificates\Form;
use App\Models\Certificate;
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


    public $isEdit = true;


    public $users;

    public $certificate_types;

    public $states;


    public function mount(Certificate $certificate)
    {


        $this->form->setCertificate($certificate);

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
        return \App\Services\PermissionCacheService::getUsersByArea('certificados');
    }




    public function save()
    {


        $this->form->update();

        session()->flash('message', 'Guardado correctamente.');

        return $this->redirectRoute("certificates.index", navigate: true);
    }
    public function render()
    {
        return view('livewire.modules.certificates.create');
    }
}
