<?php

namespace App\Http\Controllers\Modules\Donations;

use App\Http\Controllers\Controller;
use App\Models\Volunteer;
use App\Models\Campaign;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class VolunteerController extends Controller
{
    public function index(Request $request)
    {
        $query = Volunteer::with(['campaign', 'status']);

        // Search
        if ($request->input('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('email', 'like', '%' . $request->input('search') . '%')
                    ->orWhere('phone', 'like', '%' . $request->input('search') . '%');
            });
        }

        // Filters
        if ($request->input('campaign_filter')) {
            $query->where('campaign_id', $request->input('campaign_filter'));
        }

        if ($request->input('status_filter')) {
            $query->where('status_id', $request->input('status_filter'));
        }

        if ($request->input('role_filter')) {
            $query->where('role', 'like', '%' . $request->input('role_filter') . '%');
        }

        if ($request->has('certified_filter') && $request->input('certified_filter') !== null && $request->input('certified_filter') !== '') {
            $query->where('certified', $request->input('certified_filter'));
        }

        if ($request->input('city_filter')) {
            $query->where('city', 'like', '%' . $request->input('city_filter') . '%');
        }

        if ($request->input('country_filter')) {
            $query->where('country', 'like', '%' . $request->input('country_filter') . '%');
        }

        $perPage = $request->input('perPage', 10);
        $volunteers = $query->orderBy('created_at', 'desc')->paginate($perPage)->withQueryString();

        $campaigns = Campaign::orderBy('name')->get();

        $statusCategory = TagCategory::where('slug', 'estado_voluntario')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Distinct options for filters if needed, but text input used in Livewire for some.
        // We can replicate Livewire behavior (text inputs) or offer selects if data allows.
        // For distinct lists:
        $roles = Volunteer::distinct()->whereNotNull('role')->pluck('role')->sort()->values();
        $cities = Volunteer::distinct()->whereNotNull('city')->pluck('city')->sort()->values();
        $countries = Volunteer::distinct()->whereNotNull('country')->pluck('country')->sort()->values();

        return Inertia::render('Donations/Volunteers/Index', [
            'volunteers' => $volunteers,
            'filters' => $request->only(['search', 'campaign_filter', 'status_filter', 'role_filter', 'certified_filter', 'city_filter', 'country_filter', 'perPage']),
            'campaigns' => $campaigns,
            'statusOptions' => $statusOptions,
            'roles' => $roles, // Providing options for easier filtering, though Livewire used text input logic primarily
            'cities' => $cities,
            'countries' => $countries,
        ]);
    }

    public function create()
    {
        $campaigns = Campaign::orderBy('name')->get();
        $statusCategory = TagCategory::where('slug', 'estado_voluntario')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return Inertia::render('Donations/Volunteers/Create', [
            'campaigns' => $campaigns,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:volunteers,email',
            'phone' => 'nullable|string|unique:volunteers,phone',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'status_id' => 'nullable|exists:tags,id',
            'certified' => 'boolean',
            'pending_file_ids' => 'nullable|array',
        ]);

        $volunteer = Volunteer::create($validated);

        // Handle file attachments
        if (!empty($validated['pending_file_ids'])) {
            if (class_exists(\App\Actions\Cloud\LinkFileAction::class)) {
                app(\App\Actions\Cloud\LinkFileAction::class)->execute($volunteer, $validated['pending_file_ids']);
            }
        }

        return redirect()->route('donations.volunteers.index')->with('success', 'Voluntario creado exitosamente.');
    }

    public function edit(Volunteer $volunteer)
    {
        $campaigns = Campaign::orderBy('name')->get();
        $statusCategory = TagCategory::where('slug', 'estado_voluntario')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return Inertia::render('Donations/Volunteers/Edit', [
            'volunteer' => $volunteer,
            'campaigns' => $campaigns,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function update(Request $request, Volunteer $volunteer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('volunteers')->ignore($volunteer->id)],
            'phone' => ['nullable', 'string', Rule::unique('volunteers')->ignore($volunteer->id)],
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'status_id' => 'nullable|exists:tags,id',
            'certified' => 'boolean',
        ]);

        $volunteer->update($validated);

        return redirect()->route('donations.volunteers.index')->with('success', 'Voluntario actualizado exitosamente.');
    }

    public function destroy(Volunteer $volunteer)
    {
        $volunteer->delete();

        return redirect()->back()->with('success', 'Voluntario eliminado exitosamente.');
    }
}
