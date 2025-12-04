<?php

namespace App\Livewire\Modules\Finances\Invoices\Providers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\TagCategory;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $status_filter = '';
    public $contact_filter = '';
    public $date_from = '';
    public $date_to = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'contact_filter' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingContactFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'status_filter',
            'contact_filter',
            'date_from',
            'date_to',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $invoice = Invoice::find($id);

        if ($invoice) {
            $invoice->delete();
            session()->flash('success', 'Factura eliminada exitosamente.');
        }
    }

    public function render()
    {
        // Get "Proveedor" relation type tag
        $relationTypeCategory = TagCategory::where('slug', 'tipo_relacion')->first();
        $proveedorTag = $relationTypeCategory
            ? Tag::where('category_id', $relationTypeCategory->id)
                ->where('name', 'Proveedor')
                ->first()
            : null;

        $query = Invoice::with(['contact.relationType', 'status', 'createdBy']);

        // Filter only invoices from contacts with relation type "Proveedor"
        // AND with code starting with "INV-PRV-"
        if ($proveedorTag) {
            $query->whereHas('contact', function ($q) use ($proveedorTag) {
                $q->where('relation_type_id', $proveedorTag->id);
            });
        }

        // Filter only provider invoices (INV-PRV-)
        $query->where('code', 'like', 'INV-PRV-%');

        // Search by code or contact name
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('contact', function ($q2) {
                        $q2->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('company', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by contact
        if ($this->contact_filter) {
            $query->where('contact_id', $this->contact_filter);
        }

        // Filter by date range
        if ($this->date_from) {
            $query->whereDate('invoice_date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->whereDate('invoice_date', '<=', $this->date_to);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')
            ->paginate($this->perPage);

        // Get options for filters
        $statusCategory = TagCategory::where('slug', 'estado_factura')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Get only provider contacts for filter
        $providerContacts = $proveedorTag
            ? Contact::where('relation_type_id', $proveedorTag->id)
                ->orderBy('name')
                ->get()
            : collect();

        return view('livewire.modules.finances.invoices.providers.index', [
            'invoices' => $invoices,
            'statusOptions' => $statusOptions,
            'providerContacts' => $providerContacts,
        ]);
    }
}
