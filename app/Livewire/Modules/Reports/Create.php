<?php

namespace App\Livewire\Modules\Reports;

use Livewire\Component;
use App\Livewire\Forms\Modules\Reports\Form;
use App\Models\Tag;
use App\Models\TagCategory;

class Create extends Component
{
    public Form $form;

    protected $listeners = [
        // 1. ESCUCHAR AL HIJO: Cuando termine de subir los archivos
        'attachments-committed' => 'finishCreation'
    ];


    public function save()
    {
        $report = $this->form->store();

        $this->dispatch('commit-attachments', [
            'id' => $report->id,
            'name' => $report->title
        ]);
    }

    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('reports.index');
    }


    public function render()
    {
        $statusCategory = TagCategory::where('slug', 'estado_reporte')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.reports.create', [
            'statusOptions' => $statusOptions,
        ]);
    }
}
