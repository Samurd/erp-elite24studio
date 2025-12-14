<?php

namespace App\Livewire\Modules\Policies\Components;

use App\Livewire\Forms\Modules\Policies\Form;
use App\Models\Permission;
use App\Models\Policy;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateOrUpdateModal extends Component
{

    use WithFileUploads;
    public $showModal = false;
    public ?Policy $policy = null;
    public Form $form;
    protected $listeners = [
        "open-create-modal" => "openModal",
        "open-edit-modal" => "openEditModal",
        "close-create-modal" => "closeModal",
        'files-updated' => '$refresh'

    ];

    public $users;

    public $policy_types;

    public $states;


    public function mount()
    {
        $policy_type = TagCategory::where('slug', 'tipo_politica')->first();
        $state_type = TagCategory::where('slug', 'estado_politica')->first();

        $this->policy_types = Tag::where('category_id', $policy_type?->id)->get() ?? [];
        $this->states = Tag::where('category_id', $state_type?->id)->get() ?? [];

        $this->users = $this->getUsersProperty();

        $this->form->issued_at = now()->format('Y-m-d');
        $this->form->reviewed_at = now()->format('Y-m-d');
    }

    public function openEditModal(Policy $policy)
    {

        $this->policy = $policy;
        $this->form->setPolicy($policy);
        $this->showModal = true;
    }


    public function openModal()
    {

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->policy = null;
        $this->form->reset();
        $this->redirectRoute("policies.index", navigate: true);
    }

    public function updatedShowModal($value)
    {
        if (!$value) {
            $this->policy = null;
            $this->form->reset();
            $this->form->issued_at = now()->format('Y-m-d');
            $this->form->reviewed_at = now()->format('Y-m-d');
            $this->cleanupTempFiles();
        }
    }

    public function save()
    {
        if (isset($this->policy)) {
            $this->form->update();
        } else {

            $this->form->store();
        }

        $this->closeModal();
        $this->cleanupTempFiles();
    }

    public function cleanupTempFiles()
    {
        $this->form->cleanupTempFiles();
    }

    public function removeTempFile($index)
    {
        $this->form->removeTempFile($index);
        $this->dispatch('files-updated');
    }

    public function removeStoredFile($id)
    {
        $this->form->removeStoredFile($id);
        $this->dispatch('files-updated');
    }

    public function getUsersProperty()
    {
        return \App\Services\PermissionCacheService::getUsersByArea('politicas');
    }


    public function render()
    {
        return view('livewire.modules.policies.components.create-or-update-modal');
    }
}
