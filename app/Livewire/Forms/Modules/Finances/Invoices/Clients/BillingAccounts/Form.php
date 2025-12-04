<?php

namespace App\Livewire\Forms\Modules\Finances\Invoices\Clients\BillingAccounts;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Invoice;
use App\Models\Contact;

class Form extends LivewireForm
{
    public ?Invoice $invoice = null;

    #[Validate('required|date')]
    public $invoice_date = '';

    #[Validate('required|string|max:255|unique:invoices,code')]
    public $code = '';

    #[Validate('required|exists:contacts,id')]
    public $contact_id = null;

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('required|integer|min:0')]
    public $total = 0;

    #[Validate('nullable|string')]
    public $method_payment = '';

    #[Validate('nullable|exists:tags,id')]
    public $status_id = null;

    #[Validate('nullable|exists:users,id')]
    public $created_by_id = null;

    // Propiedades para manejo de archivos
    #[Validate(['nullable', 'array'])]
    #[Validate(['files.*' => 'file|max:10240'])] // 10MB max
    public $files = [];

    public $selected_folder_id = null;
    public $linked_file_ids = [];

    public function setInvoice(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->invoice_date = $invoice->invoice_date ? $invoice->invoice_date->format('Y-m-d') : '';
        $this->code = $invoice->code;
        $this->contact_id = $invoice->contact_id;
        $this->description = $invoice->description;
        $this->total = $invoice->total;
        $this->method_payment = $invoice->method_payment;
        $this->status_id = $invoice->status_id;
        $this->created_by_id = $invoice->created_by_id;
        $this->created_by_id = $invoice->created_by_id;
    }


    public function generateCode()
    {
        // Generate unique alphanumeric code like "CC-20251124-ABCD" (Cuenta de Cobro)
        $date = date('Ymd');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));

        $code = "CC-{$date}-{$random}";

        // Ensure uniqueness
        $counter = 1;
        while (Invoice::where('code', $code)->exists()) {
            $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
            $code = "CC-{$date}-{$random}";
            $counter++;

            if ($counter > 100) {
                // Fallback to timestamp if we can't find a unique code
                $code = "CC-{$date}-" . strtoupper(substr(md5(microtime()), 0, 6));
                break;
            }
        }

        return $code;
    }

    public function store()
    {
        // Generate code if not provided
        if (empty($this->code)) {
            $this->code = $this->generateCode();
        }

        $this->validate();

        $invoice = Invoice::create([
            'invoice_date' => $this->invoice_date,
            'code' => $this->code,
            'contact_id' => $this->contact_id,
            'description' => $this->description,
            'total' => $this->total,
            'method_payment' => $this->method_payment,
            'status_id' => $this->status_id,
            'created_by_id' => $this->created_by_id ?? auth()->id(),
        ]);

        return $invoice;


    }

    public function update()
    {
        // Update validation rules to exclude current invoice from unique check
        $this->validate([
            'invoice_date' => 'required|date',
            'code' => 'required|string|max:255|unique:invoices,code,' . $this->invoice->id,
            'contact_id' => 'required|exists:contacts,id',
            'description' => 'nullable|string',
            'total' => 'required|integer|min:0',
            'method_payment' => 'nullable|string',
            'status_id' => 'nullable|exists:tags,id',
            'created_by_id' => 'nullable|exists:users,id',
        ]);

        $this->invoice->update([
            'invoice_date' => $this->invoice_date,
            'code' => $this->code,
            'contact_id' => $this->contact_id,
            'description' => $this->description,
            'total' => $this->total,
            'method_payment' => $this->method_payment,
            'status_id' => $this->status_id,
            'created_by_id' => $this->created_by_id ?? auth()->id(),
        ]);

    }
}
