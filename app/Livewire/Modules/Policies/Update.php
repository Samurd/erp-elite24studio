<?php

namespace App\Livewire\Modules\Policies;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Livewire\Forms\Modules\Policies\Form;
use App\Models\Policy;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;

class Update extends Component
{
    use WithFileUploads;

    public Form $form;
    public Policy $policy;


    public function mount(Policy $policy)
    {
        $this->policy = $policy;
        $this->form->setPolicy($policy);
    }

    public function save()
    {

        $this->form->update();

        session()->flash('success', 'Política actualizada exitosamente.');

        return redirect()->route('policies.show', $this->policy->id);
    }

    public function render()
    {
        // Get type options
        $typeCategory = TagCategory::where('slug', 'tipo_politica')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        // Get status options
        $statusCategory = TagCategory::where('slug', 'estado_politica')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Get user options for assignment
        $userOptions = User::all();

        return view('livewire.modules.policies.update', [
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'userOptions' => $userOptions,
            'isEdit' => true,
        ]);
    }
}
