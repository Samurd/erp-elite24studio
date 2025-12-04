<?php

namespace App\Livewire\Modules\Donations\Donations;

use Livewire\Component;
use App\Livewire\Forms\Modules\Donations\Donations\Form;
use App\Models\Donation;
use App\Models\Campaign;

class Update extends Component
{
    public Form $form;

    public Donation $donation;

    public function mount(Donation $donation)
    {
        $this->donation = $donation;
        $this->form->setDonation($donation);
    }

    public function render()
    {
        $campaigns = Campaign::orderBy('name')->get();

        return view('livewire.modules.donations.donations.update', [
            'campaigns' => $campaigns,
        ]);
    }

    public function save()
    {
        $this->form->update($this->donation);
    }
}
