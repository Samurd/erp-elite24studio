<?php

namespace App\Livewire\Modules\Stages;

use App\Livewire\Forms\Modules\Stages\Form;
use Livewire\Component;

class Create extends Component
{
    public Form $form;

    public function save()
    {
        $this->form->store();
        
        return redirect()->route('stages.index');
    }
    
    public function render()
    {
        return view('livewire.modules.stages.create');
    }
}