<?php

namespace App\Http\Controllers\Modules\Invoices;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProvidersController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $statusFilter = $request->get('status_filter', '');
        $contactFilter = $request->get('contact_filter', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $perPage = $request->get('perPage', 10);

        // Get "Proveedor" relation type tag
        $relationTypeCategory = TagCategory::where('slug', 'tipo_relacion')->first();
        $proveedorTag = $relationTypeCategory
            ? Tag::where('category_id', $relationTypeCategory->id)
                ->where('name', 'Proveedor')
                ->first()
            : null;

        $query = Invoice::with(['contact.relationType', 'status', 'createdBy']);

        // Filter only invoices from contacts with relation type "Proveedor"
        if ($proveedorTag) {
            $query->whereHas('contact', function ($q) use ($proveedorTag) {
                $q->where('relation_type_id', $proveedorTag->id);
            });
        }

        // Filter only provider invoices (INV-PRV-)
        $query->where('code', 'like', 'INV-PRV-%');

        // Search by code or contact name
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                    ->orWhereHas('contact', function ($q2) use ($search) {
                        $q2->where('name', 'like', '%' . $search . '%')
                            ->orWhere('company', 'like', '%' . $search . '%');
                    });
            });
        }

        // Filter by status
        if ($statusFilter) {
            $query->where('status_id', $statusFilter);
        }

        // Filter by contact
        if ($contactFilter) {
            $query->where('contact_id', $contactFilter);
        }

        // Filter by date range
        if ($dateFrom) {
            $query->whereDate('invoice_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('invoice_date', '<=', $dateTo);
        }

        $invoices = $query->orderBy('invoice_date', 'desc')->paginate($perPage);

        // Get options for filters
        $statusCategory = TagCategory::where('slug', 'estado_factura')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Get only provider contacts for filter
        $providerContacts = $proveedorTag
            ? Contact::where('relation_type_id', $proveedorTag->id)
                ->orderBy('name')
                ->get()
            : collect();

        return Inertia::render('Invoices/Providers/Index', [
            'invoices' => $invoices,
            'statusOptions' => $statusOptions,
            'providerContacts' => $providerContacts,
            'filters' => [
                'search' => $search,
                'status_filter' => $statusFilter,
                'contact_filter' => $contactFilter,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'perPage' => $perPage,
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('Invoices/Providers/Form', [
            'invoice' => null,
        ]);
    }

    public function show(Invoice $invoiceProvider)
    {
        $invoiceProvider->load(['contact', 'status', 'createdBy', 'files']);

        return Inertia::render('Invoices/Providers/Show', [
            'invoice' => $invoiceProvider,
        ]);
    }
}
