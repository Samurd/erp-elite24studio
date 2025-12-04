<?php

namespace App\Livewire\Modules\Worksites\Changes;

use App\Livewire\Forms\Modules\Worksites\Changes\Form;
use App\Models\Change;
use App\Models\Worksite;
use App\Models\User;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    public Change $change;
    public Worksite $worksite;

    public function mount(Change $change)
    {
        $this->change = $change;
        $this->worksite = $change->worksite;
        $this->form->setChange($change);
    }

    public function save()
    {
        $this->form->update($this->change);
        
        return redirect()->route('worksites.show', $this->worksite->id);
    }
    
    public function render()
    {
        // Obtener usuarios para el selector de responsables
        $responsibles = User::orderBy('name')->get();

        // Obtener opciones para los tipos de cambio usando TagCategory
        $changeTypeCategory = TagCategory::where('slug', 'tipo_cambio')->first();
        $changeTypeOptions = $changeTypeCategory ? Tag::where('category_id', $changeTypeCategory->id)->get() : collect();
        
        // Obtener opciones para los estados usando TagCategory
        $statusCategory = TagCategory::where('slug', 'estado_cambio')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Obtener opciones para el impacto en presupuesto usando TagCategory
        $budgetImpactCategory = TagCategory::where('slug', 'impacto_presupuesto')->first();
        $budgetImpactOptions = $budgetImpactCategory ? Tag::where('category_id', $budgetImpactCategory->id)->get() : collect();

        return view('livewire.modules.worksites.changes.update', [
            'worksite' => $this->worksite,
            'change' => $this->change,
            'responsibles' => $responsibles,
            'changeTypeOptions' => $changeTypeOptions,
            'statusOptions' => $statusOptions,
            'budgetImpactOptions' => $budgetImpactOptions,
        ]);
    }
}
