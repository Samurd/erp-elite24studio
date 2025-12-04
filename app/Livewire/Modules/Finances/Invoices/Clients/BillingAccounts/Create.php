<?php

namespace App\Livewire\Modules\Finances\Invoices\Clients\BillingAccounts;

use Livewire\Component;
use App\Livewire\Forms\Modules\Finances\Invoices\Clients\BillingAccounts\Form;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public Form $form;

    protected $listeners = [
        // 1. ESCUCHAR AL HIJO: Cuando termine de subir los archivos
        'attachments-committed' => 'finishCreation'
    ];




    public function mount()
    {
        // Auto-generate code when creating
        $this->form->code = $this->form->generateCode();
        // Set default date to today
        $this->form->invoice_date = date('Y-m-d');
    }



    public function save()
    {

        $invoice = $this->form->store();

        $invoiceName = "Cuenta de cobro: " . $invoice->code;

        $this->dispatch('commit-attachments', [
            'id' => $invoice->id,
            'name' => $invoiceName
        ]);

    }

    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

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
            'isEdit' => false,
        ]);
    }
}
