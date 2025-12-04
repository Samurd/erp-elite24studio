<?php

namespace App\Livewire\Modules\Licenses;

use App\Livewire\Forms\Modules\Licenses\Form;
use App\Models\Project;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public Form $form;

    protected $listeners = [
        // 1. ESCUCHAR AL HIJO: Cuando termine de subir los archivos
        'attachments-committed' => 'finishCreation'
    ];


    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('licenses.index');
    }



    public function save()
    {

        $license = $this->form->store();

        $projectName = $license->project ? $license->project->name : 'Sin Proyecto';
        $newLicenseName = "Licencia " . $license->id . " - " . $projectName;

        $this->dispatch('commit-attachments', [
            'id' => $license->id,
            'name' => $newLicenseName
        ]);
    }


    public function render()
    {
        // Obtener opciones para los filtros usando TagCategory
        $licenseTypeCategory = TagCategory::where('slug', 'tipo_licencia')->first();
        $licenseTypeOptions = $licenseTypeCategory ? Tag::where('category_id', $licenseTypeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_licencia')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Obtener proyectos
        $projects = Project::orderBy('name')->get();

        return view('livewire.modules.licenses.create', [
            'licenseTypeOptions' => $licenseTypeOptions,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'isEdit' => false,
        ]);
    }
}
