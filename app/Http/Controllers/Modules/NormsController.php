<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Norm;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NormsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $perPage = $request->get('perPage', 10);

        $query = Norm::query()->with('files');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $norms = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return Inertia::render('Norms/Index', [
            'norms' => $norms,
            'filters' => [
                'search' => $search,
                'perPage' => $perPage,
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('Norms/Form', [
            'norm' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $validated['user_id'] = auth()->id();

        $norm = Norm::create($validated);

        // Handle file attachments
        if ($request->has('pending_file_ids') && is_array($request->pending_file_ids)) {
            \App\Actions\LinkFileAction::run($norm, $request->pending_file_ids);
        }

        return redirect()->route('finances.norms.index');
    }

    public function edit(Norm $norm)
    {
        $norm->load('files');

        return Inertia::render('Norms/Form', [
            'norm' => $norm,
        ]);
    }

    public function update(Request $request, Norm $norm)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $norm->update($validated);

        // Handle file attachments
        if ($request->has('pending_file_ids') && is_array($request->pending_file_ids)) {
            \App\Actions\LinkFileAction::run($norm, $request->pending_file_ids);
        }

        return redirect()->route('finances.norms.index');
    }

    public function show(Norm $norm)
    {
        $norm->load(['files', 'user']);

        return Inertia::render('Norms/Show', [
            'norm' => $norm,
        ]);
    }

    public function destroy(Norm $norm)
    {
        $norm->delete();
        return redirect()->route('finances.norms.index');
    }
}
