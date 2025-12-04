<?php

namespace App\Livewire\Modules\Marketing\AdPieces;

use App\Livewire\Forms\Modules\Marketing\AdPieces\Form;
use App\Models\Adpiece;
use App\Models\Project;
use App\Models\Team;
use App\Models\Strategy;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    public Adpiece $adpiece;

    public function mount(Adpiece $adpiece)
    {
        $this->adpiece = $adpiece;
        $this->form->setAdpiece($adpiece);
    }

    public function save()
    {
        $this->form->update($this->adpiece);
        
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
            'adpiece' => $this->adpiece,
        ]);
    }
}
