<?php

namespace App\Livewire\Modules\Donations\ApuCampaigns;

use App\Livewire\Forms\Modules\Donations\ApuCampaigns\Form;
use App\Models\Campaign;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;

class Create extends Component
{
    public Form $form;

    public function updatedFormQuantity()
    {
        $this->form->calculateTotal();
    }

    public function updatedFormUnitPrice()
    {
        $this->form->calculateTotal();
    }

    public function save()
    {
        $this->form->store();

        session()->flash('success', 'Registro creado exitosamente.');

        return redirect()->route('donations.apu-campaigns.index');
    }

    public function render()
    {
        $campaigns = Campaign::orderBy('name')->get();

        $unitCategory = TagCategory::where('slug', 'unidad')->first();
        $unitOptions = $unitCategory ? Tag::where('category_id', $unitCategory->id)->get() : collect();

        return view('livewire.modules.donations.apu-campaigns.create', [
            'campaigns' => $campaigns,
            'unitOptions' => $unitOptions,
        ]);
    }
}
