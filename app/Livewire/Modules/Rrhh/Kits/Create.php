<?php

namespace App\Livewire\Modules\Rrhh\Kits;

use Livewire\Component;
use App\Livewire\Forms\Modules\Rrhh\Kits\Form;
use App\Models\User;
use App\Models\Tag;
use App\Models\TagCategory;

class Create extends Component
{
    public Form $form;

    public function mount()
    {
        // Set default request date to today
        $this->form->request_date = date('Y-m-d');
        // Set requested by to current user
        $this->form->requested_by_user_id = auth()->id();
    }

    public function save()
    {
        $this->form->store();

        session()->flash('success', 'Kit creado exitosamente.');

        return redirect()->route('rrhh.kits.index');
    }

    public function render()
    {
        // Get users for dropdowns
        $users = User::orderBy('name')->get();

        // Get status options
        $statusCategory = TagCategory::where('slug', 'estado_kit')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.rrhh.kits.create', [
            'users' => $users,
            'statusOptions' => $statusOptions,
        ]);
    }
}
