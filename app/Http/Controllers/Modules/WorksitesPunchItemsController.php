<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Worksite;
use App\Models\PunchItem;
use App\Models\User;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorksitesPunchItemsController extends Controller
{
    public function create(Worksite $worksite)
    {
        return Inertia::render('Worksites/PunchItems/Create', [
            'worksite' => $worksite,
            'statusOptions' => $this->getStatusOptions(),
            'responsibles' => $this->getResponsibles(),
        ]);
    }

    public function store(Request $request, Worksite $worksite)
    {
        $validated = $request->validate([
            'status_id' => ['nullable', 'exists:tags,id'],
            'observations' => ['required', 'string'],
            'responsible_id' => ['nullable', 'exists:users,id'],
        ], [
            'observations.required' => 'Las observaciones son obligatorias.',
            'status_id.exists' => 'El estado seleccionado no es válido.',
            'responsible_id.exists' => 'El responsable seleccionado no es válido.',
        ]);

        $validated['worksite_id'] = $worksite->id;

        PunchItem::create($validated);

        return redirect()->route('worksites.show', $worksite->id)
            ->with('success', 'Punch Item creado exitosamente.');
    }

    public function show(Worksite $worksite, PunchItem $punchItem)
    {
        $punchItem->load(['responsible', 'status', 'files']);

        return Inertia::render('Worksites/PunchItems/Show', [
            'worksite' => $worksite,
            'punchItem' => $punchItem,
        ]);
    }

    public function edit(Worksite $worksite, PunchItem $punchItem)
    {
        return Inertia::render('Worksites/PunchItems/Edit', [
            'worksite' => $worksite,
            'punchItem' => $punchItem,
            'statusOptions' => $this->getStatusOptions(),
            'responsibles' => $this->getResponsibles(),
        ]);
    }

    public function update(Request $request, Worksite $worksite, PunchItem $punchItem)
    {
        $validated = $request->validate([
            'status_id' => ['nullable', 'exists:tags,id'],
            'observations' => ['required', 'string'],
            'responsible_id' => ['nullable', 'exists:users,id'],
        ], [
            'observations.required' => 'Las observaciones son obligatorias.',
            'status_id.exists' => 'El estado seleccionado no es válido.',
            'responsible_id.exists' => 'El responsable seleccionado no es válido.',
        ]);

        $punchItem->update($validated);

        return redirect()->route('worksites.show', $worksite->id)
            ->with('success', 'Punch Item actualizado exitosamente.');
    }

    public function destroy(Worksite $worksite, PunchItem $punchItem)
    {
        $punchItem->delete();

        return redirect()->route('worksites.show', $worksite->id)
            ->with('success', 'Punch Item eliminado exitosamente.');
    }

    protected function getStatusOptions()
    {
        $category = TagCategory::where('slug', 'estado_punch_item')->first();
        return $category ? Tag::where('category_id', $category->id)->get() : collect();
    }

    protected function getResponsibles()
    {
        return User::orderBy('name')->get();
    }
}
