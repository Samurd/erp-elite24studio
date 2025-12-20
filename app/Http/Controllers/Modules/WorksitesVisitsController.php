<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Worksite;
use App\Models\Visit;
use App\Models\User;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorksitesVisitsController extends Controller
{
    public function create(Worksite $worksite)
    {
        return Inertia::render('Worksites/Visits/Create', [
            'worksite' => $worksite,
            'visitStatusOptions' => $this->getVisitStatusOptions(),
            'visitorOptions' => $this->getVisitorOptions(),
        ]);
    }

    public function store(Request $request, Worksite $worksite)
    {
        $validated = $request->validate([
            'visit_date' => ['required', 'date'],
            'performed_by' => ['nullable', 'exists:users,id'],
            'status_id' => ['nullable', 'exists:tags,id'],
            'general_observations' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
        ], [
            'visit_date.required' => 'La fecha de visita es obligatoria.',
            'visit_date.date' => 'La fecha de visita debe ser una fecha válida.',
            'performed_by.exists' => 'El visitante seleccionado no es válido.',
            'status_id.exists' => 'El estado seleccionado no es válido.',
        ]);

        $validated['worksite_id'] = $worksite->id;

        Visit::create($validated);

        return redirect()->route('worksites.show', $worksite->id)
            ->with('success', 'Visita creada exitosamente.');
    }

    public function show(Worksite $worksite, Visit $visit)
    {
        $visit->load(['visitor', 'status']);

        return Inertia::render('Worksites/Visits/Show', [
            'worksite' => $worksite,
            'visit' => $visit,
        ]);
    }

    public function edit(Worksite $worksite, Visit $visit)
    {
        return Inertia::render('Worksites/Visits/Edit', [
            'worksite' => $worksite,
            'visit' => $visit,
            'visitStatusOptions' => $this->getVisitStatusOptions(),
            'visitorOptions' => $this->getVisitorOptions(),
        ]);
    }

    public function update(Request $request, Worksite $worksite, Visit $visit)
    {
        $validated = $request->validate([
            'visit_date' => ['required', 'date'],
            'performed_by' => ['nullable', 'exists:users,id'],
            'status_id' => ['nullable', 'exists:tags,id'],
            'general_observations' => ['nullable', 'string'],
            'internal_notes' => ['nullable', 'string'],
        ], [
            'visit_date.required' => 'La fecha de visita es obligatoria.',
            'visit_date.date' => 'La fecha de visita debe ser una fecha válida.',
            'performed_by.exists' => 'El visitante seleccionado no es válido.',
            'status_id.exists' => 'El estado seleccionado no es válido.',
        ]);

        // Ensure worksite_id doesn't change implicitly or accidentally, though we are not updating it here.
        // If we wanted to allow moving visits between worksites, we would include it. 
        // For now, keeping it simple as per original logic.

        $visit->update($validated);

        return redirect()->route('worksites.show', $worksite->id)
            ->with('success', 'Visita actualizada exitosamente.');
    }

    public function destroy(Worksite $worksite, Visit $visit)
    {
        $visit->delete();

        return redirect()->route('worksites.show', $worksite->id)
            ->with('success', 'Visita eliminada exitosamente.');
    }

    protected function getVisitStatusOptions()
    {
        $category = TagCategory::where('slug', 'estado_visita')->first();
        return $category ? Tag::where('category_id', $category->id)->get() : collect();
    }

    protected function getVisitorOptions()
    {
        return User::orderBy('name')->get();
    }
}
