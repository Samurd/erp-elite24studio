<?php

namespace App\Livewire\Modules\Finances\Invoices\Providers;

use Livewire\Component;
use App\Livewire\Forms\Modules\Finances\Invoices\Providers\Form;
use App\Models\Invoice;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\WithFileUploads;

class Update extends Component
{
    use WithFileUploads;

    public Form $form;
    public Invoice $invoiceProvider;


    public function mount(Invoice $invoiceProvider)
    {
        $this->invoiceProvider = $invoiceProvider;
        $this->form->setInvoice($invoiceProvider);
    }

    public function save()
    {


        $this->form->update();

        session()->flash('success', 'Factura de proveedor actualizada exitosamente.');

        return redirect()->route('finances.invoices.providers.index');
    }

    public function render()
    {
        // Get only provider contacts
        $relationTypeCategory = TagCategory::where('slug', 'tipo_relacion')->first();
        $proveedorTag = $relationTypeCategory
            ? Tag::where('category_id', $relationTypeCategory->id)
                ->where('name', 'Proveedor')
                ->first()
            : null;

        $providerContacts = $proveedorTag
            ? Contact::where('relation_type_id', $proveedorTag->id)
                ->orderBy('name')
                ->get()
            : collect();

        // Get status options
        $statusCategory = TagCategory::where('slug', 'estado_factura')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.finances.invoices.providers.create', [
            'providerContacts' => $providerContacts,
            'statusOptions' => $statusOptions,
            'isEdit' => true,
        ]);
    }
}
