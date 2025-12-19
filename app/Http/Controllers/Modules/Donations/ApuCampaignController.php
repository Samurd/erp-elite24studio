<?php

namespace App\Http\Controllers\Modules\Donations;

use App\Http\Controllers\Controller;
use App\Models\ApuCampaign;
use App\Models\Campaign;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class ApuCampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = ApuCampaign::with(['campaign', 'unit']);

        // Search
        if ($request->input('search')) {
            $query->where('description', 'like', '%' . $request->input('search') . '%');
        }

        // Filters
        if ($request->input('campaign_filter')) {
            $query->where('campaign_id', $request->input('campaign_filter'));
        }

        if ($request->input('unit_filter')) {
            $query->where('unit_id', $request->input('unit_filter'));
        }

        $perPage = $request->input('perPage', 10);
        $apuCampaigns = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        $campaigns = Campaign::orderBy('name')->get();

        // Try 'unidad' first, fallback to 'unidad_medida' to cover both existing code cases
        $unitCategory = TagCategory::where('slug', 'unidad')->first()
            ?? TagCategory::where('slug', 'unidad_medida')->first();
        $unitOptions = $unitCategory ? Tag::where('category_id', $unitCategory->id)->get() : collect();

        return Inertia::render('Donations/ApuCampaigns/Index', [
            'apuCampaigns' => $apuCampaigns,
            'filters' => $request->only(['search', 'campaign_filter', 'unit_filter', 'perPage']),
            'campaigns' => $campaigns,
            'unitOptions' => $unitOptions,
        ]);
    }

    public function create()
    {
        $campaigns = Campaign::orderBy('name')->get();
        $unitCategory = TagCategory::where('slug', 'unidad')->first()
            ?? TagCategory::where('slug', 'unidad_medida')->first();
        $unitOptions = $unitCategory ? Tag::where('category_id', $unitCategory->id)->get() : collect();

        return Inertia::render('Donations/ApuCampaigns/Create', [
            'campaigns' => $campaigns,
            'unitOptions' => $unitOptions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_id' => 'nullable|exists:tags,id',
            'unit_price' => 'required|numeric|min:0', // Cents
            'total_price' => 'nullable|numeric|min:0', // Cents, optional as we can calc it
        ]);

        // Ensure total_price is consistent
        $validated['total_price'] = $validated['quantity'] * $validated['unit_price'];

        ApuCampaign::create($validated);

        return redirect()->route('donations.apu-campaigns.index')->with('success', 'Registro creado exitosamente.');
    }

    public function edit(ApuCampaign $apuCampaign)
    {
        $campaigns = Campaign::orderBy('name')->get();
        $unitCategory = TagCategory::where('slug', 'unidad')->first()
            ?? TagCategory::where('slug', 'unidad_medida')->first();
        $unitOptions = $unitCategory ? Tag::where('category_id', $unitCategory->id)->get() : collect();

        return Inertia::render('Donations/ApuCampaigns/Edit', [
            'apuCampaign' => $apuCampaign,
            'campaigns' => $campaigns,
            'unitOptions' => $unitOptions,
        ]);
    }

    public function update(Request $request, ApuCampaign $apuCampaign)
    {
        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'unit_id' => 'nullable|exists:tags,id',
            'unit_price' => 'required|numeric|min:0',
            'total_price' => 'nullable|numeric|min:0',
        ]);

        // Ensure total_price is consistent
        $validated['total_price'] = $validated['quantity'] * $validated['unit_price'];

        $apuCampaign->update($validated);

        return redirect()->route('donations.apu-campaigns.index')->with('success', 'Registro actualizado exitosamente.');
    }

    public function destroy(ApuCampaign $apuCampaign)
    {
        $apuCampaign->delete();

        return redirect()->back()->with('success', 'Registro eliminado exitosamente.');
    }
}
