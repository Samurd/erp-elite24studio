<?php

namespace App\Livewire\Modules\Donations\Campaigns;

use App\Livewire\Forms\Modules\Donations\Campaigns\Form;
use App\Models\Campaign;
use App\Models\User;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    public Campaign $campaign;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;
        $this->form->setCampaign($campaign);
    }

    public function save()
    {
        $this->form->update($this->campaign);
        
        return redirect()->route('donations.campaigns.index');
    }
    
    public function render()
    {
        // Obtener opciones para los filtros usando TagCategory
        $statusCategory = TagCategory::where('slug', 'estado_campana')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        
        // Obtener usuarios para el selector de responsables
        $users = User::orderBy('name')->get();

        return view('livewire.modules.donations.campaigns.create', [
            'statusOptions' => $statusOptions,
            'users' => $users,
            'campaign' => $this->campaign,
        ]);
    }
}
