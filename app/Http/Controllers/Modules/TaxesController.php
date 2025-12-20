<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\TaxRecord;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaxesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $typeFilter = $request->get('type_filter', '');
        $statusFilter = $request->get('status_filter', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $perPage = $request->get('perPage', 10);

        $query = TaxRecord::with(['type', 'status', 'files']);

        // Search by Entity
        if ($search) {
            $query->where('entity', 'like', '%' . $search . '%');
        }

        // Filter by type
        if ($typeFilter) {
            $query->where('type_id', $typeFilter);
        }

        // Filter by status
        if ($statusFilter) {
            $query->where('status_id', $statusFilter);
        }

        // Filter by date range
        if ($dateFrom) {
            $query->whereDate('date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('date', '<=', $dateTo);
        }

        $taxes = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $typeCategory = TagCategory::where('slug', 'tipo_impuesto')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_impuesto')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return Inertia::render('Taxes/Index', [
            'taxes' => $taxes,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'filters' => [
                'search' => $search,
                'type_filter' => $typeFilter,
                'status_filter' => $statusFilter,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'perPage' => $perPage,
            ]
        ]);
    }

    public function create()
    {
        $typeCategory = TagCategory::where('slug', 'tipo_impuesto')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_impuesto')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return Inertia::render('Taxes/Form', [
            'taxRecord' => null,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_id' => 'nullable|exists:tags,id',
            'status_id' => 'nullable|exists:tags,id',
            'entity' => 'required|string|max:255',
            'base' => 'required|integer|min:0',
            'porcentage' => 'required|integer|min:0|max:100',
            'amount' => 'required|integer|min:0',
            'date' => 'required|date',
            'observations' => 'nullable|string',
        ]);

        $taxRecord = TaxRecord::create($validated);

        // Handle file attachments if pending_file_ids is provided
        if ($request->has('pending_file_ids') && is_array($request->pending_file_ids)) {
            \App\Actions\LinkFileAction::run($taxRecord, $request->pending_file_ids);
        }

        return redirect()->route('finances.taxes.index');
    }

    public function edit(TaxRecord $taxRecord)
    {
        $taxRecord->load('files');

        $typeCategory = TagCategory::where('slug', 'tipo_impuesto')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_impuesto')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return Inertia::render('Taxes/Form', [
            'taxRecord' => $taxRecord,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function update(Request $request, TaxRecord $taxRecord)
    {
        $validated = $request->validate([
            'type_id' => 'nullable|exists:tags,id',
            'status_id' => 'nullable|exists:tags,id',
            'entity' => 'required|string|max:255',
            'base' => 'required|integer|min:0',
            'porcentage' => 'required|integer|min:0|max:100',
            'amount' => 'required|integer|min:0',
            'date' => 'required|date',
            'observations' => 'nullable|string',
        ]);

        $taxRecord->update($validated);

        // Handle file attachments if pending_file_ids is provided
        if ($request->has('pending_file_ids') && is_array($request->pending_file_ids)) {
            \App\Actions\LinkFileAction::run($taxRecord, $request->pending_file_ids);
        }

        return redirect()->route('finances.taxes.index');
    }

    public function show(TaxRecord $taxRecord)
    {
        $taxRecord->load(['type', 'status', 'files']);

        return Inertia::render('Taxes/Show', [
            'taxRecord' => $taxRecord,
        ]);
    }

    public function destroy(TaxRecord $taxRecord)
    {
        $taxRecord->delete();
        return redirect()->route('finances.taxes.index');
    }
}
