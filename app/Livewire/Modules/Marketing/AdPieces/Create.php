<?php

namespace App\Livewire\Modules\Marketing\AdPieces;

use App\Livewire\Forms\Modules\Marketing\AdPieces\Form;
use App\Models\Project;
use App\Models\Team;
use App\Models\Strategy;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;

class Create extends Component
{
    public Form $form;

    protected $listeners = [
        // 1. ESCUCHAR AL HIJO: Cuando termine de subir los archivos
        'attachments-committed' => 'finishCreation'
    ];


    public function save()
    {
        $piece = $this->form->store();

        $this->dispatch('commit-attachments', [
            'id' => $piece->id,
            'name' => $piece->name
        ]);


    }

    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('marketing.ad-pieces.index');
    }

    public function render()
    {
        // Obtener opciones para los filtros usando TagCategory
        $typeCategory = TagCategory::where('slug', 'tipo_pieza')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $formatCategory = TagCategory::where('slug', 'formato')->first();
        $formatOptions = $formatCategory ? Tag::where('category_id', $formatCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_pieza')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Obtener proyectos, equipos y estrategias
        $projects = Project::orderBy('name')->get();
        $teams = Team::orderBy('name')->get();
        $strategies = Strategy::orderBy('name')->get();

        return view('livewire.modules.marketing.ad-pieces.create', [
            'typeOptions' => $typeOptions,
            'formatOptions' => $formatOptions,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'teams' => $teams,
            'strategies' => $strategies,
        ]);
    }
}
