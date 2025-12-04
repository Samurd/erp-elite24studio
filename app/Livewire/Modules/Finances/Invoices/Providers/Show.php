<?php

namespace App\Livewire\Modules\Finances\Invoices\Providers;

use Livewire\Component;
use App\Models\Invoice;

class Show extends Component
{
    public Invoice $invoiceProvider;

    public function mount(Invoice $invoiceProvider)
    {
        $this->invoiceProvider = $invoiceProvider;
    }

    public function delete()
    {
        $this->invoiceProvider->delete();

        session()->flash('success', 'Factura de proveedor eliminada exitosamente.');

        return redirect()->route('finances.invoices.providers.index');
    }

    public function render()
    {
        return view('livewire.modules.finances.invoices.providers.show');
    }
}
