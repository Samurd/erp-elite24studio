<?php

namespace App\Livewire\Forms\Modules\Donations\Campaigns;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Campaign;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Support\Facades\Auth;

class Form extends LivewireForm
{
    public $name;
    public $date_event;
    public $address;
    public $responsible_id;
    public $status_id;
    public $alliances;
    public $goal;
    public $estimated_budget;
    public $description;

    protected $rules = [
        'name' => 'required|string|max:255',
        'date_event' => 'nullable|date',
        'address' => 'nullable|string|max:255',
        'responsible_id' => 'nullable|exists:users,id',
        'status_id' => 'nullable|exists:tags,id',
        'alliances' => 'nullable|string',
        'goal' => 'nullable|integer|min:0',
        'estimated_budget' => 'nullable|integer|min:0',
        'description' => 'nullable|string',
    ];

    public function store()
    {
        $this->validate();

        $campaign = Campaign::create([
            'name' => $this->name,
            'date_event' => $this->date_event,
            'address' => $this->address,
            'responsible_id' => $this->responsible_id,
            'status_id' => $this->status_id,
            'alliances' => $this->alliances,
            'goal' => $this->goal,
            'estimated_budget' => $this->estimated_budget,
            'description' => $this->description,
        ]);

        session()->flash('success', 'Campaña creada exitosamente.');

        return redirect()->route('donations.campaigns.index');
    }

    public function update(Campaign $campaign)
    {
        $this->validate();

        $campaign->update([
            'name' => $this->name,
            'date_event' => $this->date_event,
            'address' => $this->address,
            'responsible_id' => $this->responsible_id,
            'status_id' => $this->status_id,
            'alliances' => $this->alliances,
            'goal' => $this->goal,
            'estimated_budget' => $this->estimated_budget,
            'description' => $this->description,
        ]);

        session()->flash('success', 'Campaña actualizada exitosamente.');

        return redirect()->route('donations.campaigns.index');
    }

    public function setCampaign(Campaign $campaign)
    {
        $this->name = $campaign->name;
        $this->date_event = $campaign->date_event?->format('Y-m-d');
        $this->address = $campaign->address;
        $this->responsible_id = $campaign->responsible_id;
        $this->status_id = $campaign->status_id;
        $this->alliances = $campaign->alliances;
        $this->goal = $campaign->goal;
        $this->estimated_budget = $campaign->estimated_budget;
        $this->description = $campaign->description;
    }
}
