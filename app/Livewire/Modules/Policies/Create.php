<?php

namespace App\Livewire\Modules\Policies;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Livewire\Forms\Modules\Policies\Form;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;

class Create extends Component
{
    use WithFileUploads;

    public Form $form;

    protected $listeners = [
        'attachments-committed' => 'finishCreation'

    ];



    public function save()
    {

        $policy = $this->form->store();

        $this->dispatch('commit-attachments', [
            'id' => $policy->id,
            'name' => $policy->name
        ]);

    }

    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('policies.index');
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
        $userOptions = \App\Services\CommonDataCacheService::getAllUsers();

        return view('livewire.modules.policies.create', [
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'userOptions' => $userOptions,
            'isEdit' => false,
        ]);
    }
}
