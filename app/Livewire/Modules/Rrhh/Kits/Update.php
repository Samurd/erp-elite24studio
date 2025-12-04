<?php

namespace App\Livewire\Modules\Rrhh\Kits;

use Livewire\Component;
use App\Livewire\Forms\Modules\Rrhh\Kits\Form;
use App\Models\Kit;
use App\Models\User;
use App\Models\Tag;
use App\Models\TagCategory;

class Update extends Component
{
    public Form $form;
    public Kit $kit;

    public function mount(Kit $kit)
    {
        $this->kit = $kit;
        $this->form->setKit($kit);
    }

    public function save()
    {
        $this->form->update();

        session()->flash('success', 'Kit actualizado exitosamente.');

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
