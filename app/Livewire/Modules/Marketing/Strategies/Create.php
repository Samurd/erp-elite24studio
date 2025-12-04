<?php

namespace App\Livewire\Modules\Marketing\Strategies;

use App\Livewire\Forms\Modules\Marketing\Strategies\Form;
use App\Models\User;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;

class Create extends Component
{
    public Form $form;

    public function save()
    {
        $this->form->store();
        
        return redirect()->route('marketing.strategies.index')
            ->with('success', 'Estrategia creada exitosamente.');
    }
    
    public function render()
    {
        // Obtener opciones para los filtros usando TagCategory
        $statusCategory = TagCategory::where('slug', 'estado_estrategia')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        
        // Obtener usuarios para el selector de responsables
        $users = User::orderBy('name')->get();

        return view('livewire.modules.marketing.strategies.create', [
            'statusOptions' => $statusOptions,
            'users' => $users,
        ]);
    }
}
