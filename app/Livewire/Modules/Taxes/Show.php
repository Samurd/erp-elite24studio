<?php

namespace App\Livewire\Modules\Taxes;

use Livewire\Component;

use App\Models\TaxRecord;

class Show extends Component
{
    public TaxRecord $taxRecord;

    public function mount(TaxRecord $taxRecord)
    {
        $this->taxRecord = $taxRecord;
    }

    public function render()
    {
        return view('livewire.modules.taxes.show');
    }
}
