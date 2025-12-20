<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Change;
use App\Models\Worksite;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use App\Actions\LinkFileAction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorksitesChangesController extends Controller
{
    public function create(Worksite $worksite)
    {
        $changeTypeCategory = TagCategory::where('slug', 'tipo_cambio')->first();
        $changeTypeOptions = $changeTypeCategory ? Tag::where('category_id', $changeTypeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_cambio')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $budgetImpactCategory = TagCategory::where('slug', 'impacto_presupuesto')->first();
        $budgetImpactOptions = $budgetImpactCategory ? Tag::where('category_id', $budgetImpactCategory->id)->get() : collect();

        $users = User::orderBy('name')->get();

        return Inertia::render('Worksites/Changes/Form', [
            'worksite' => $worksite,
            'change' => null,
            'changeTypeOptions' => $changeTypeOptions,
            'statusOptions' => $statusOptions,
            'budgetImpactOptions' => $budgetImpactOptions,
            'users' => $users,
        ]);
    }

    public function store(Request $request, Worksite $worksite)
    {
        $validated = $request->validate([
            'change_date' => 'required|date',
            'change_type_id' => 'required|exists:tags,id',
            'requested_by' => 'nullable|string|max:255',
            'description' => 'required|string',
            'budget_impact_id' => 'nullable|exists:tags,id',
            'status_id' => 'nullable|exists:tags,id',
            'approved_by' => 'nullable|exists:users,id',
            'internal_notes' => 'nullable|string',
            'pending_file_ids' => 'array',
        ]);

        $validated['worksite_id'] = $worksite->id;

        $change = Change::create($validated);

        if (!empty($validated['pending_file_ids'])) {
            $linkFileAction = app(LinkFileAction::class);
            $linkFileAction->execute($validated['pending_file_ids'], $change);
        }

        return redirect()->route('worksites.show', $worksite->id)->with('success', 'Cambio creado exitosamente.');
    }

    public function edit(Worksite $worksite, Change $change)
    {
        $changeTypeCategory = TagCategory::where('slug', 'tipo_cambio')->first();
        $changeTypeOptions = $changeTypeCategory ? Tag::where('category_id', $changeTypeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_cambio')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $budgetImpactCategory = TagCategory::where('slug', 'impacto_presupuesto')->first();
        $budgetImpactOptions = $budgetImpactCategory ? Tag::where('category_id', $budgetImpactCategory->id)->get() : collect();

        $users = User::orderBy('name')->get();

        return Inertia::render('Worksites/Changes/Form', [
            'worksite' => $worksite,
            'change' => $change,
            'changeTypeOptions' => $changeTypeOptions,
            'statusOptions' => $statusOptions,
            'budgetImpactOptions' => $budgetImpactOptions,
            'users' => $users,
        ]);
    }

    public function update(Request $request, Worksite $worksite, Change $change)
    {
        $validated = $request->validate([
            'change_date' => 'required|date',
            'change_type_id' => 'required|exists:tags,id',
            'requested_by' => 'nullable|string|max:255',
            'description' => 'required|string',
            'budget_impact_id' => 'nullable|exists:tags,id',
            'status_id' => 'nullable|exists:tags,id',
            'approved_by' => 'nullable|exists:users,id',
            'internal_notes' => 'nullable|string',
        ]);

        $change->update($validated);

        // Files are handled via ModelAttachments component directly for existing models

        return redirect()->route('worksites.show', $worksite->id)->with('success', 'Cambio actualizado exitosamente.');
    }

    public function show(Worksite $worksite, Change $change)
    {
        $change->load(['type', 'status', 'budgetImpact', 'approver', 'files']);

        return Inertia::render('Worksites/Changes/Show', [
            'worksite' => $worksite,
            'change' => $change,
        ]);
    }

    public function destroy(Worksite $worksite, Change $change)
    {
        $change->delete();
        return redirect()->route('worksites.show', $worksite->id)->with('success', 'Cambio eliminado exitosamente.');
    }
}
