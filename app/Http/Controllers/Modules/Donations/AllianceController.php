<?php

namespace App\Http\Controllers\Modules\Donations;

use App\Http\Controllers\Controller;
use App\Models\Alliance;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class AllianceController extends Controller
{
    public function index(Request $request)
    {
        $query = Alliance::with(['type']);

        // Search
        if ($request->input('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        // Filters
        if ($request->input('type_filter')) {
            $query->where('type_id', $request->input('type_filter'));
        }

        if ($request->has('certified_filter') && $request->input('certified_filter') !== null && $request->input('certified_filter') !== '') {
            $query->where('certified', $request->input('certified_filter'));
        }

        if ($request->input('date_from')) {
            $query->whereDate('start_date', '>=', $request->input('date_from'));
        }

        if ($request->input('date_to')) {
            $query->whereDate('start_date', '<=', $request->input('date_to'));
        }

        $perPage = $request->input('perPage', 10);
        $alliances = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        $typeCategory = TagCategory::where('slug', 'tipo_alianza')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        return Inertia::render('Donations/Alliances/Index', [
            'alliances' => $alliances,
            'filters' => $request->only(['search', 'type_filter', 'certified_filter', 'date_from', 'date_to', 'perPage']),
            'typeOptions' => $typeOptions,
        ]);
    }

    public function create()
    {
        $typeCategory = TagCategory::where('slug', 'tipo_alianza')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        return Inertia::render('Donations/Alliances/Create', [
            'typeOptions' => $typeOptions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type_id' => 'nullable|exists:tags,id',
            'start_date' => 'required|date',
            'validity' => 'nullable|integer|min:1',
            'certified' => 'boolean',
            'pending_file_ids' => 'nullable|array',
        ]);

        $alliance = Alliance::create($validated);

        // Handle file attachments
        if (!empty($validated['pending_file_ids'])) {
            if (class_exists(\App\Actions\Cloud\LinkFileAction::class)) {
                app(\App\Actions\Cloud\LinkFileAction::class)->execute($alliance, $validated['pending_file_ids']);
            }
        }

        return redirect()->route('donations.alliances.index')->with('success', 'Alianza creada exitosamente.');
    }

    public function edit(Alliance $alliance)
    {
        $typeCategory = TagCategory::where('slug', 'tipo_alianza')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        return Inertia::render('Donations/Alliances/Edit', [
            'alliance' => $alliance,
            'typeOptions' => $typeOptions,
        ]);
    }

    public function update(Request $request, Alliance $alliance)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type_id' => 'nullable|exists:tags,id',
            'start_date' => 'required|date',
            'validity' => 'nullable|integer|min:1',
            'certified' => 'boolean',
        ]);

        $alliance->update($validated);

        return redirect()->route('donations.alliances.index')->with('success', 'Alianza actualizada exitosamente.');
    }

    public function destroy(Alliance $alliance)
    {
        $alliance->delete();

        return redirect()->back()->with('success', 'Alianza eliminada exitosamente.');
    }
}
