<?php

namespace App\Livewire\Modules\Finances\Invoices\Clients\BillingAccounts;

use Livewire\Component;
use App\Models\Invoice;

class Show extends Component
{
    public Invoice $billingAccount;

    public function mount(Invoice $billingAccount)
    {
        $this->billingAccount = $billingAccount;
    }

    public function delete()
    {
        $this->billingAccount->delete();

        session()->flash('success', 'Cuenta de cobro eliminada exitosamente.');

        return redirect()->route('finances.invoices.clients.billing-accounts.index');
    }

    public function render()
    {
        return view('livewire.modules.finances.invoices.clients.billing-accounts.show');
    }
}
