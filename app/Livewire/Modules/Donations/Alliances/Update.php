<?php

namespace App\Livewire\Modules\Donations\Alliances;

use App\Livewire\Forms\Modules\Donations\Alliances\Form;
use App\Models\Alliance;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    public Alliance $alliance;

    public function mount(Alliance $alliance)
    {
        $this->alliance = $alliance;
        $this->form->setAlliance($alliance);
    }

    public function save()
    {
        $this->form->update();

        session()->flash('success', 'Alianza actualizada exitosamente.');

        return redirect()->route('donations.alliances.index');
    }

    public function render()
    {
        // Obtener opciones para el tipo de alianza
        $typeCategory = TagCategory::where('slug', 'tipo_alianza')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        return view('livewire.modules.donations.alliances.create', [
            'typeOptions' => $typeOptions,
        ]);
    }
}
