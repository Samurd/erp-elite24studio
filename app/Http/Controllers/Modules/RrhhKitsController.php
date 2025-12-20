<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Kit;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RrhhKitsController extends Controller
{
    public function index(Request $request)
    {
        $query = Kit::with(['requestedByUser', 'status', 'deliveryResponsibleUser']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('recipient_name', 'like', '%' . $request->search . '%')
                    ->orWhere('recipient_role', 'like', '%' . $request->search . '%')
                    ->orWhere('position_area', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status_filter) {
            $query->where('status_id', $request->status_filter);
        }

        if ($request->date_from) {
            $query->whereDate('request_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('request_date', '<=', $request->date_to);
        }

        $kits = $query->orderBy('request_date', 'desc')
            ->paginate($request->perPage ?? 10)
            ->withQueryString();

        return Inertia::render('Rrhh/Kits/Index', [
            'kits' => $kits,
            'statusOptions' => $this->getTags('estado_kit'),
            'filters' => $request->all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Rrhh/Kits/Form', [
            'kit' => null,
            'users' => User::orderBy('name')->get(),
            'statusOptions' => $this->getTags('estado_kit'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'requested_by_user_id' => 'nullable|exists:users,id',
            'position_area' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'recipient_role' => 'required|string|max:255',
            'kit_type' => 'nullable|string|max:255',
            'kit_contents' => 'nullable|string',
            'request_date' => 'required|date',
            'delivery_date' => 'nullable|date',
            'status_id' => 'nullable|exists:tags,id',
            'delivery_responsible_user_id' => 'nullable|exists:users,id',
            'observations' => 'nullable|string',
        ]);

        if (empty($validated['requested_by_user_id'])) {
            $validated['requested_by_user_id'] = auth()->id();
        }

        Kit::create($validated);

        return redirect()->route('rrhh.kits.index')->with('success', 'Kit creado exitosamente.');
    }

    public function edit(Kit $kit)
    {
        return Inertia::render('Rrhh/Kits/Form', [
            'kit' => $kit,
            'users' => User::orderBy('name')->get(),
            'statusOptions' => $this->getTags('estado_kit'),
        ]);
    }

    public function update(Request $request, Kit $kit)
    {
        $validated = $request->validate([
            'requested_by_user_id' => 'nullable|exists:users,id',
            'position_area' => 'required|string|max:255',
            'recipient_name' => 'required|string|max:255',
            'recipient_role' => 'required|string|max:255',
            'kit_type' => 'nullable|string|max:255',
            'kit_contents' => 'nullable|string',
            'request_date' => 'required|date',
            'delivery_date' => 'nullable|date',
            'status_id' => 'nullable|exists:tags,id',
            'delivery_responsible_user_id' => 'nullable|exists:users,id',
            'observations' => 'nullable|string',
        ]);

        $kit->update($validated);

        return redirect()->route('rrhh.kits.index')->with('success', 'Kit actualizado exitosamente.');
    }

    public function destroy(Kit $kit)
    {
        $kit->delete();
        return redirect()->route('rrhh.kits.index')->with('success', 'Kit eliminado exitosamente.');
    }

    private function getTags($slug)
    {
        $category = TagCategory::where('slug', $slug)->first();
        return $category ? Tag::where('category_id', $category->id)->get() : collect();
    }
}
