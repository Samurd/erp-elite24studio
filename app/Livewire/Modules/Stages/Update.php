<?php

namespace App\Livewire\Modules\Stages;

use App\Livewire\Forms\Modules\Stages\Form;
use App\Models\Stage;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    public Stage $stage;

    public function mount(Stage $stage)
    {
        $this->stage = $stage;
        $this->form->setStage($stage);
    }

    public function save()
    {
        $this->form->update($this->stage);
        
        return redirect()->route('stages.index');
    }
    
    public function render()
    {
        return view('livewire.modules.stages.create', [
            'stage' => $this->stage,
        ]);
    }
}