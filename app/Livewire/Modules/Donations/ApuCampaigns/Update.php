<?php

namespace App\Livewire\Modules\Donations\ApuCampaigns;

use App\Livewire\Forms\Modules\Donations\ApuCampaigns\Form;
use App\Models\ApuCampaign;
use App\Models\Campaign;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    public ApuCampaign $apuCampaign;

    public function mount(ApuCampaign $apuCampaign)
    {
        $this->apuCampaign = $apuCampaign;
        $this->form->setApuCampaign($apuCampaign);
    }

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
        $this->form->update();

        session()->flash('success', 'Registro actualizado exitosamente.');

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
