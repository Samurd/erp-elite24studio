<?php

namespace App\Livewire\Forms\Modules\Donations\ApuCampaigns;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\ApuCampaign;

class Form extends LivewireForm
{
    public ?ApuCampaign $apuCampaign = null;

    #[Validate('nullable|exists:campaigns,id')]
    public $campaign_id = null;

    #[Validate('required|string')]
    public $description = '';

    #[Validate('required|integer|min:1')]
    public $quantity = null;

    #[Validate('nullable|exists:tags,id')]
    public $unit_id = null;

    #[Validate('nullable|integer|min:0')]
    public $unit_price = null;

    #[Validate('nullable|integer|min:0')]
    public $total_price = null;

    public function setApuCampaign(ApuCampaign $apuCampaign)
    {
        $this->apuCampaign = $apuCampaign;
        $this->campaign_id = $apuCampaign->campaign_id;
        $this->description = $apuCampaign->description;
        $this->quantity = $apuCampaign->quantity;
        $this->unit_id = $apuCampaign->unit_id;
        $this->unit_price = $apuCampaign->unit_price;
        $this->total_price = $apuCampaign->total_price;
    }

    public function store()
    {
        $this->validate();

        // Calculate total price if not set or ensure it's consistent?
        // For now, let's trust the input or calculate it here if needed.
        // If unit_price and quantity are present, we can calculate total_price.
        if ($this->unit_price && $this->quantity) {
            $this->total_price = $this->unit_price * $this->quantity;
        }

        ApuCampaign::create([
            'campaign_id' => $this->campaign_id,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unit_id' => $this->unit_id,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
        ]);
    }

    public function update()
    {
        $this->validate();

        if ($this->unit_price && $this->quantity) {
            $this->total_price = $this->unit_price * $this->quantity;
        }

        $this->apuCampaign->update([
            'campaign_id' => $this->campaign_id,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unit_id' => $this->unit_id,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
        ]);
    }

    public function calculateTotal()
    {
        if (is_numeric($this->quantity) && is_numeric($this->unit_price)) {
            $this->total_price = $this->quantity * $this->unit_price;
        }
    }
}
