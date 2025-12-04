<?php

namespace App\Livewire\Modules\Worksites;

use App\Livewire\Forms\Modules\Worksites\Form;
use App\Models\Worksite;
use App\Models\Project;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    public Worksite $worksite;

    public function mount(Worksite $worksite)
    {
        $this->worksite = $worksite;
        $this->form->setWorksite($worksite);
    }

    public function save()
    {
        $this->form->update($this->worksite);
        
        return redirect()->route('worksites.index');
    }
    
    public function render()
    {
        // Obtener opciones para los filtros usando TagCategory
        $typeCategory = TagCategory::where('slug', 'tipo_obra')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();
        
        $statusCategory = TagCategory::where('slug', 'estado_obra')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        
        // Obtener proyectos
        $projects = Project::orderBy('name')->get();

        // Obtener usuarios para el selector de responsables
        $responsibles = User::orderBy('name')->get();

        return view('livewire.modules.worksites.create', [
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'responsibles' => $responsibles,
            'worksite' => $this->worksite,
        ]);
    }
}
