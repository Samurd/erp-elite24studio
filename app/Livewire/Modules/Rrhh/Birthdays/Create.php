<?php

namespace App\Livewire\Modules\Rrhh\Birthdays;

use Livewire\Component;
use App\Livewire\Forms\Modules\Rrhh\Birthdays\Form;
use App\Models\User;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Project;

class Create extends Component
{
    public Form $form;

    public function mount()
    {
        // Set responsible to current user
        $this->form->responsible_id = auth()->id();
    }

    public function save()
    {
        $this->form->store();

        session()->flash('success', 'Cumpleaños creado exitosamente.');

        return redirect()->route('rrhh.birthdays.index');
    }

    public function render()
    {
        // Get users for responsible select
        $users = User::orderBy('name')->get();

        // Get employees
        $employees = \App\Models\Employee::orderBy('full_name')->get();

        // Get contacts
        $contacts = \App\Models\Contact::orderBy('name')->get();

        return view('livewire.modules.rrhh.birthdays.create', [
            'users' => $users,
            'employees' => $employees,
            'contacts' => $contacts,
        ]);
    }
}
