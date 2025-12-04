<?php

namespace App\Livewire\Modules\Donations\Donations;

use Livewire\Component;
use App\Livewire\Forms\Modules\Donations\Donations\Form;
use App\Models\Campaign;

class Create extends Component
{
    public Form $form;

    protected $listeners = [
        // 1. ESCUCHAR AL HIJO: Cuando termine de subir los archivos
        'attachments-committed' => 'finishCreation'
    ];

    public function render()
    {
        $campaigns = Campaign::orderBy('name')->get();

        return view('livewire.modules.donations.donations.create', [
            'campaigns' => $campaigns,
        ]);
    }

    public function save()
    {
        $donation = $this->form->store();


        $this->dispatch('commit-attachments', [
            'id' => $donation->id,
            'name' => $donation->name
        ]);
    }

    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('donations.donations.index');
    }
}
