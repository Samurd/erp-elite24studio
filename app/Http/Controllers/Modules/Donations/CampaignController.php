<?php

namespace App\Http\Controllers\Modules\Donations;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $query = Campaign::with(['responsible', 'status'])
            ->withCount('donations')
            ->withSum('donations as total_donations_amount', 'amount');

        // Search
        if ($request->input('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('alliances', 'like', '%' . $search . '%');
            });
        }

        // Filter by status
        if ($request->input('status_filter')) {
            $query->where('status_id', $request->input('status_filter'));
        }

        // Filter by responsible
        if ($request->input('responsible_filter')) {
            $query->where('responsible_id', $request->input('responsible_filter'));
        }

        // Filter by date range
        if ($request->input('date_from')) {
            $query->whereDate('date_event', '>=', $request->input('date_from'));
        }

        if ($request->input('date_to')) {
            $query->whereDate('date_event', '<=', $request->input('date_to'));
        }

        $perPage = $request->input('perPage', 10);
        $campaigns = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        // Options for filters
        $statusCategory = TagCategory::where('slug', 'estado_campana')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        $responsibleOptions = User::orderBy('name')->get();

        return Inertia::render('Donations/Campaigns/Index', [
            'campaigns' => $campaigns,
            'filters' => $request->only(['search', 'status_filter', 'responsible_filter', 'date_from', 'date_to', 'perPage']),
            'statusOptions' => $statusOptions,
            'responsibleOptions' => $responsibleOptions,
        ]);
    }

    public function create()
    {
        $statusCategory = TagCategory::where('slug', 'estado_campana')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        $users = User::orderBy('name')->get();

        return Inertia::render('Donations/Campaigns/Create', [
            'statusOptions' => $statusOptions,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date_event' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'responsible_id' => 'nullable|exists:users,id',
            'status_id' => 'nullable|exists:tags,id',
            'alliances' => 'nullable|string',
            'goal' => 'nullable|integer|min:0',
            'estimated_budget' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        Campaign::create($validated);

        return redirect()->route('donations.campaigns.index')->with('success', 'Campaña creada exitosamente.');
    }

    public function edit(Campaign $campaign)
    {
        $statusCategory = TagCategory::where('slug', 'estado_campana')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        $users = User::orderBy('name')->get();

        return Inertia::render('Donations/Campaigns/Edit', [
            'campaign' => $campaign,
            'statusOptions' => $statusOptions,
            'users' => $users,
        ]);
    }

    public function update(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date_event' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'responsible_id' => 'nullable|exists:users,id',
            'status_id' => 'nullable|exists:tags,id',
            'alliances' => 'nullable|string',
            'goal' => 'nullable|integer|min:0',
            'estimated_budget' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $campaign->update($validated);

        return redirect()->route('donations.campaigns.index')->with('success', 'Campaña actualizada exitosamente.');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();

        return redirect()->back()->with('success', 'Campaña eliminada exitosamente.');
    }
}
