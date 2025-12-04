<?php

namespace App\Livewire\Modules\Finances\Norms;

use App\Livewire\Forms\Modules\Finances\Norms\Form;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public Form $form;



    protected $listeners = [
        'attachments-committed' => 'finishCreation'

    ];


    public function save()
    {

        $norm = $this->form->store();

        $this->dispatch('commit-attachments', [
            'id' => $norm->id,
            'name' => $norm->name
        ]);
    }


    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('finances.norms.index');
    }

    public function render()
    {
        return view('livewire.modules.finances.norms.create', [
            'isEdit' => false,
        ]);
    }
}
