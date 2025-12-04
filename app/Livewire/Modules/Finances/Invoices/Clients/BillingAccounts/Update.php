<?php

namespace App\Livewire\Modules\Finances\Invoices\Clients\BillingAccounts;

use Livewire\Component;
use App\Livewire\Forms\Modules\Finances\Invoices\Clients\BillingAccounts\Form;
use App\Models\Invoice;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\WithFileUploads;

class Update extends Component
{
    use WithFileUploads;

    public Form $form;
    public Invoice $billingAccount;


    public function mount(Invoice $billingAccount)
    {
        $this->billingAccount = $billingAccount;
        $this->form->setInvoice($billingAccount);
    }



    public function save()
    {


        $this->form->update();

        session()->flash('success', 'Cuenta de cobro actualizada exitosamente.');

        return redirect()->route('finances.invoices.clients.billing-accounts.index');
    }

    public function render()
    {
        // Get only client contacts
        $relationTypeCategory = TagCategory::where('slug', 'tipo_relacion')->first();
        $clienteTag = $relationTypeCategory
            ? Tag::where('category_id', $relationTypeCategory->id)
                ->where('name', 'Cliente')
                ->first()
            : null;

        $clientContacts = $clienteTag
            ? Contact::where('relation_type_id', $clienteTag->id)
                ->orderBy('name')
                ->get()
            : collect();

        // Get status options
        $statusCategory = TagCategory::where('slug', 'estado_factura')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.finances.invoices.clients.billing-accounts.create', [
            'clientContacts' => $clientContacts,
            'statusOptions' => $statusOptions,
            'isEdit' => true,
        ]);
    }
}
