<?php

namespace App\Livewire\Modules\Marketing\Strategies;

use App\Livewire\Forms\Modules\Marketing\Strategies\Form;
use App\Models\Strategy;
use App\Models\User;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    public Strategy $strategy;

    public function mount(Strategy $strategy)
    {
        $this->strategy = $strategy;
        $this->form->setStrategy($strategy);
    }

    public function save()
    {
        $this->form->update($this->strategy);
        
        return redirect()->route('marketing.strategies.index')
            ->with('success', 'Estrategia actualizada exitosamente.');
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
            'strategy' => $this->strategy,
        ]);
    }
}
