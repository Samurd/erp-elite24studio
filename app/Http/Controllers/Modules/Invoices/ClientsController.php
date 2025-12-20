<?php

namespace App\Http\Controllers\Modules\Invoices;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $statusFilter = $request->get('status_filter', '');
        $contactFilter = $request->get('contact_filter', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $perPage = $request->get('perPage', 10);

        // Get "Cliente" relation type tag
        $relationTypeCategory = TagCategory::where('slug', 'tipo_relacion')->first();
        $clienteTag = $relationTypeCategory
            ? Tag::where('category_id', $relationTypeCategory->id)
                ->where('name', 'Cliente')
                ->first()
            : null;

        $query = Invoice::with(['contact.relationType', 'status', 'createdBy', 'files']);

        // Filter only invoices from contacts with relation type "Cliente"
        if ($clienteTag) {
            $query->whereHas('contact', function ($q) use ($clienteTag) {
                $q->where('relation_type_id', $clienteTag->id);
            });
        }

        // Exclude billing accounts (CC-) and provider invoices (INV-PRV-)
        $query->where('code', 'like', 'INV-%')
            ->where('code', 'not like', 'INV-PRV-%');

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

        // Get only client contacts for filter
        $clientContacts = $clienteTag
            ? Contact::where('relation_type_id', $clienteTag->id)
                ->orderBy('name')
                ->get()
            : collect();

        return Inertia::render('Invoices/Clients/Index', [
            'invoices' => $invoices,
            'statusOptions' => $statusOptions,
            'clientContacts' => $clientContacts,
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

        // Generate code
        $code = $this->generateCode();

        return Inertia::render('Invoices/Clients/Form', [
            'invoice' => null,
            'clientContacts' => $clientContacts,
            'statusOptions' => $statusOptions,
            'generatedCode' => $code,
        ]);
    }

    private function generateCode()
    {
        $date = date('Ymd');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
        $code = "INV-{$date}-{$random}";

        $counter = 1;
        while (Invoice::where('code', $code)->exists()) {
            $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
            $code = "INV-{$date}-{$random}";
            $counter++;

            if ($counter > 100) {
                $code = "INV-{$date}-" . strtoupper(substr(md5(microtime()), 0, 6));
                break;
            }
        }

        return $code;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'code' => 'required|string|max:255|unique:invoices,code',
            'contact_id' => 'required|exists:contacts,id',
            'description' => 'nullable|string',
            'total_amount' => 'required|integer|min:0',
            'method_payment' => 'nullable|string',
            'status_id' => 'nullable|exists:tags,id',
        ]);

        $validated['created_by_id'] = auth()->id();

        $invoice = Invoice::create($validated);

        // Handle file attachments
        if ($request->has('pending_file_ids') && is_array($request->pending_file_ids)) {
            \App\Actions\LinkFileAction::run($invoice, $request->pending_file_ids);
        }

        return redirect()->route('finances.invoices.clients.index');
    }

    public function edit(Invoice $invoiceClient)
    {
        $invoiceClient->load('files');

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

        return Inertia::render('Invoices/Clients/Form', [
            'invoice' => $invoiceClient,
            'clientContacts' => $clientContacts,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function update(Request $request, Invoice $invoiceClient)
    {
        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'code' => 'required|string|max:255|unique:invoices,code,' . $invoiceClient->id,
            'contact_id' => 'required|exists:contacts,id',
            'description' => 'nullable|string',
            'total_amount' => 'required|integer|min:0',
            'method_payment' => 'nullable|string',
            'status_id' => 'nullable|exists:tags,id',
        ]);

        $invoiceClient->update($validated);

        // Handle file attachments
        if ($request->has('pending_file_ids') && is_array($request->pending_file_ids)) {
            \App\Actions\LinkFileAction::run($invoiceClient, $request->pending_file_ids);
        }

        return redirect()->route('finances.invoices.clients.index');
    }

    public function show(Invoice $invoiceClient)
    {
        $invoiceClient->load(['contact', 'status', 'createdBy', 'files']);

        return Inertia::render('Invoices/Clients/Show', [
            'invoice' => $invoiceClient,
        ]);
    }

    public function destroy(Invoice $invoiceClient)
    {
        $invoiceClient->delete();
        return redirect()->route('finances.invoices.clients.index');
    }
}
