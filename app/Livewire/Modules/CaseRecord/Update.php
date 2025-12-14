<?php

namespace App\Livewire\Modules\CaseRecord;

use App\Livewire\Forms\Modules\CaseRecord\Form;
use App\Models\CaseRecord;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class Update extends Component
{
    use WithFileUploads;

    public Form $form;

    public $contacts;
    public $case_types;
    public $users;
    public $states;
    public $defaultUserId;
    public $isEdit = true;

    public function mount(CaseRecord $caseRecord)
    {
        $this->form->setCaseRecord($caseRecord);

        $state_type = TagCategory::where("slug", "estado_caso")->first();
        $case_type = TagCategory::where("slug", "tipo_caso")->first();

        $this->contacts = \App\Services\CommonDataCacheService::getAllContacts();
        $this->case_types = Tag::where("category_id", $case_type->id)->get();
        $this->states = Tag::where("category_id", $state_type->id)->get();

        // Obtener usuarios (cached)
        $this->users = \App\Services\CommonDataCacheService::getAllUsers();
        $this->defaultUserId = Auth::user()->id;
    }

    public function save()
    {

        $this->form->update();

        session()->flash("success", "Actualizado exitosamente!");

        $this->redirectRoute("case-record.index", navigate: true);
    }

    public function render()
    {
        return view("livewire.modules.case-record.create");
    }
}
