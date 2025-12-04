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

class Create extends Component
{
    use WithFileUploads;

    public Form $form;

    public $defaultUserId;
    public $isEdit = false;


    protected $listeners = [
        'attachments-committed' => 'finishCreation'

    ];

    /**
     * Abrir selector de carpetas
     */


    public function save()
    {


        $caseRecord = $this->form->store();

        $this->dispatch('commit-attachments', [
            'id' => $caseRecord->id,
            'name' => $caseRecord->name
        ]);
    }

    public function finishCreation()
    {
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('case-record.index');
    }

    public function mount()
    {
        $this->form->date = date("Y-m-d");

        $this->defaultUserId = Auth::user()->id;
    }

    public function render()
    {
        $contacts = Contact::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        $statusCategory = TagCategory::where('slug', 'estado_caso')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $priorityCategory = TagCategory::where('slug', 'prioridad_caso')->first();
        $priorityOptions = $priorityCategory ? Tag::where('category_id', $priorityCategory->id)->get() : collect();

        $caseTypeCategory = TagCategory::where('slug', 'tipo_caso')->first();
        $caseTypes = $caseTypeCategory ? Tag::where('category_id', $caseTypeCategory->id)->get() : collect();

        return view('livewire.modules.case-record.create', [
            'contacts' => $contacts,
            'users' => $users,
            'states' => $statusOptions,
            'priorityOptions' => $priorityOptions,
            'case_types' => $caseTypes,
            'defaultUserId' => $this->defaultUserId,
            'isEdit' => false,
        ]);
    }
}
