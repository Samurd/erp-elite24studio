<?php

namespace App\Livewire\Modules\Worksites\PunchItems;

use App\Livewire\Forms\Modules\Worksites\PunchItems\Form;
use App\Models\PunchItem;
use App\Models\Worksite;
use App\Models\User;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    public PunchItem $punchItem;
    public Worksite $worksite;

    public function mount(PunchItem $punchItem)
    {
        $this->punchItem = $punchItem;
        $this->worksite = $punchItem->worksite;
        $this->form->setPunchItem($punchItem);
    }

    public function save()
    {
        $this->form->update($this->punchItem);
        
        return redirect()->route('worksites.show', $this->worksite->id);
    }
    
    public function render()
    {
        // Obtener usuarios para el selector de responsables
        $responsibles = User::orderBy('name')->get();

        // Obtener opciones para los estados usando TagCategory
        $statusCategory = TagCategory::where('slug', 'estado_punch_item')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.worksites.punch-items.create', [
            'worksite' => $this->worksite,
            'punchItem' => $this->punchItem,
            'responsibles' => $responsibles,
            'statusOptions' => $statusOptions,
        ]);
    }
}
