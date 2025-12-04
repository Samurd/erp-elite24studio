<?php

namespace App\Livewire\Modules\Finances\Invoices\Clients;

use Livewire\Component;
use App\Models\Invoice;

class Show extends Component
{
    public Invoice $invoiceClient;

    public function mount(Invoice $invoiceClient)
    {
        $this->invoiceClient = $invoiceClient;
    }

    public function delete()
    {
        $this->invoiceClient->delete();

        session()->flash('success', 'Factura eliminada exitosamente.');

        return redirect()->route('finances.invoices.clients.index');
    }

    public function render()
    {
        return view('livewire.modules.finances.invoices.clients.show');
    }
}
