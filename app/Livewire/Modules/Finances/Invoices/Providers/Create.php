<?php

namespace App\Livewire\Modules\Finances\Invoices\Providers;

use Livewire\Component;
use App\Livewire\Forms\Modules\Finances\Invoices\Providers\Form;
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

        $invoiceProvider = $this->form->store();

        $invoiceName = "Factura proveedor: " . $invoiceProvider->code;

        $this->dispatch('commit-attachments', [
            'id' => $invoiceProvider->id,
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
            'isEdit' => false,
        ]);
    }
}
