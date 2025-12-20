<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Project;
use App\Models\Stage;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ProjectsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $statusFilter = $request->get('status_filter', '');
        $projectTypeFilter = $request->get('project_type_filter', '');
        $contactFilter = $request->get('contact_filter', '');
        $responsibleFilter = $request->get('responsible_filter', '');
        $currentStageFilter = $request->get('current_stage_filter', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $perPage = $request->get('perPage', 10);

        $query = Project::with([
            'contact',
            'status',
            'projectType',
            'currentStage',
            'responsible'
        ]);

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('direction', 'like', '%' . $search . '%');
            });
        }

        // Filters
        if ($statusFilter)
            $query->where('status_id', $statusFilter);
        if ($projectTypeFilter)
            $query->where('project_type_id', $projectTypeFilter);
        if ($contactFilter)
            $query->where('contact_id', $contactFilter);
        if ($responsibleFilter)
            $query->where('responsible_id', $responsibleFilter);
        if ($currentStageFilter)
            $query->where('current_stage_id', $currentStageFilter);

        // Date Range
        if ($dateFrom)
            $query->whereDate('created_at', '>=', $dateFrom);
        if ($dateTo)
            $query->whereDate('created_at', '<=', $dateTo);

        $projects = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Options
        $statusCategory = TagCategory::where('slug', 'estado_proyecto')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $projectTypeCategory = TagCategory::where('slug', 'tipo_proyecto')->first();
        $projectTypeOptions = $projectTypeCategory ? Tag::where('category_id', $projectTypeCategory->id)->get() : collect();

        $contacts = \App\Services\CommonDataCacheService::getAllContacts();
        $responsibleOptions = \App\Services\CommonDataCacheService::getAllUsers();
        $stageOptions = \App\Services\CommonDataCacheService::getAllStages();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'statusOptions' => $statusOptions,
            'projectTypeOptions' => $projectTypeOptions,
            'contactOptions' => $contacts,
            'responsibleOptions' => $responsibleOptions,
            'stageOptions' => $stageOptions,
            'filters' => [
                'search' => $search,
                'status_filter' => $statusFilter,
                'project_type_filter' => $projectTypeFilter,
                'contact_filter' => $contactFilter,
                'responsible_filter' => $responsibleFilter,
                'current_stage_filter' => $currentStageFilter,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'perPage' => $perPage,
            ]
        ]);
    }

    public function create()
    {
        $statusCategory = TagCategory::where('slug', 'estado_proyecto')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $projectTypeCategory = TagCategory::where('slug', 'tipo_proyecto')->first();
        $projectTypeOptions = $projectTypeCategory ? Tag::where('category_id', $projectTypeCategory->id)->get() : collect();

        $contacts = Contact::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $teams = Team::orderBy('name')->get();
        $stages = Stage::orderBy('name')->get();

        return Inertia::render('Projects/Form', [
            'project' => null,
            'statusOptions' => $statusOptions,
            'projectTypeOptions' => $projectTypeOptions,
            'contacts' => $contacts,
            'users' => $users,
            'teams' => $teams,
            'stages' => $stages,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'direction' => 'nullable|string|max:255',
            'contact_id' => 'nullable|exists:contacts,id',
            'status_id' => 'nullable|exists:tags,id',
            'project_type_id' => 'nullable|exists:tags,id',
            'current_stage_id' => 'nullable', // Can be null or ID
            'initial_stage_id' => 'nullable', // Used in create
            'responsible_id' => 'nullable|exists:users,id',
            'team_id' => 'nullable|exists:teams,id',
            'temp_stages' => 'nullable|array', // New stages to create
            'temp_stages.*.name' => 'required_with:temp_stages|string',
            'temp_stages.*.description' => 'nullable|string',
        ]);

        // Create Project
        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'direction' => $validated['direction'],
            'contact_id' => $validated['contact_id'],
            'status_id' => $validated['status_id'],
            'project_type_id' => $validated['project_type_id'],
            'responsible_id' => $validated['responsible_id'],
            'team_id' => $validated['team_id'],
            'current_stage_id' => $validated['current_stage_id'], // Will be updated if it matches a temp stage
        ]);

        // Handle Temp Stages
        $tempStageIdMap = [];
        if (!empty($validated['temp_stages'])) {
            foreach ($validated['temp_stages'] as $tempStage) {
                $newStage = Stage::create([
                    'project_id' => $project->id,
                    'name' => $tempStage['name'],
                    'description' => $tempStage['description'] ?? null,
                ]);
                // Map temp ID (if client provided one to link initial_stage_id) to real ID
                if (isset($tempStage['id'])) {
                    $tempStageIdMap[$tempStage['id']] = $newStage->id;
                }
            }
        }

        // Handle initial_stage_id if it was set
        $initialStageId = $validated['initial_stage_id'];
        if ($initialStageId) {
            // Check if it's a temp ID mapping
            if (isset($tempStageIdMap[$initialStageId])) {
                $project->update(['current_stage_id' => $tempStageIdMap[$initialStageId]]);
            } elseif (is_numeric($initialStageId)) {
                $project->update(['current_stage_id' => $initialStageId]);
            }
        }

        // Handle file attachments
        if ($request->has('pending_file_ids') && is_array($request->pending_file_ids)) {
            \App\Actions\Cloud\Files\LinkFileAction::run($project, $request->pending_file_ids);
        }

        return redirect()->route('projects.index')->with('success', 'Proyecto creado exitosamente.');
    }

    public function edit(Project $project)
    {
        $project->load(['files']);

        $statusCategory = TagCategory::where('slug', 'estado_proyecto')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $projectTypeCategory = TagCategory::where('slug', 'tipo_proyecto')->first();
        $projectTypeOptions = $projectTypeCategory ? Tag::where('category_id', $projectTypeCategory->id)->get() : collect();

        $contacts = Contact::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $teams = Team::orderBy('name')->get();

        // Existing global stages for selection + this project's specific stages
        // The View logic seemed to show all stages.
        // Let's stick to showing all stages for selection.
        $stages = Stage::orderBy('name')->get();

        return Inertia::render('Projects/Form', [
            'project' => $project,
            'statusOptions' => $statusOptions,
            'projectTypeOptions' => $projectTypeOptions,
            'contacts' => $contacts,
            'users' => $users,
            'teams' => $teams,
            'stages' => $stages,
            'projectStages' => $project->stages, // Stages specifically belonging to this project if filtered? 
            // In Livewire Create: existingStages = Stage::all().
            // In Livewire Update: existingStages = Stage::all().
            // So 'stages' covers it.
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'direction' => 'nullable|string|max:255',
            'contact_id' => 'nullable|exists:contacts,id',
            'status_id' => 'nullable|exists:tags,id',
            'project_type_id' => 'nullable|exists:tags,id',
            'current_stage_id' => 'nullable',
            'responsible_id' => 'nullable|exists:users,id',
            'team_id' => 'nullable|exists:teams,id',
            'managed_stages' => 'nullable|array', // Stages to sync
        ]);

        $project->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'direction' => $validated['direction'],
            'contact_id' => $validated['contact_id'],
            'status_id' => $validated['status_id'],
            'project_type_id' => $validated['project_type_id'],
            'current_stage_id' => $validated['current_stage_id'],
            'responsible_id' => $validated['responsible_id'],
            'team_id' => $validated['team_id'],
        ]);

        // Update plans team_id
        if (isset($validated['team_id'])) {
            $project->plans()->update(['team_id' => $validated['team_id']]);
        }

        // Sync Stages logic
        // We receive a list of stages that SHOULD exist for this project (or generally).
        // The Livewire logic seemed to allow creating new global stages or project-bound stages.
        // Looking at Create.php: Stage::create(['project_id' => $project->id ...])
        // It seems stages ARE bound to projects.
        // Wait, if stages are bound to projects, why did Livewire fetch Stage::all()?
        // `App\Models\Stage` has `project_id`.
        // If `Stage::all()` fetches everyone's stages, that's messy.
        // Livewire `Create.php`: `$this->existingStages = Stage::orderBy('name')->get();`
        // Maybe stages are shared? Let's check `Stage` model.
        // Ah, if `project_id` is nullable, they might be shared templates.

        // Let's assume for this specific helper we handle upsert of stages passed in `managed_stages` 
        // that are meant to be *this* project's specific stages (if any).

        // Based on `Update.php`: `saveStage` creates with `project_id`. `deleteStage` deletes.
        // So we can support adding/removing stages here.
        if (isset($validated['managed_stages'])) {
            $currentIds = collect($validated['managed_stages'])->pluck('id')->filter(function ($id) {
                return is_numeric($id);
            })->toArray();

            // Delete stages belonging to this project not in the list
            // Only if we want to enforce full sync. 
            // Better to just handle additions/updates for safety unless we're sure.
            // Let's implement robust sync:
            // 1. Update existing ones
            // 2. Create new ones (temp IDs)
            // 3. Delete removed ones? (Only if we are sending the FULL list of project stages)

            // For now, let's just handle Create/Update of stages sent.
            foreach ($validated['managed_stages'] as $stageData) {
                if (Str::startsWith($stageData['id'], 'temp_')) {
                    Stage::create([
                        'project_id' => $project->id,
                        'name' => $stageData['name'],
                        'description' => $stageData['description'] ?? null,
                    ]);
                } elseif (is_numeric($stageData['id'])) {
                    Stage::where('id', $stageData['id'])->update([
                        'name' => $stageData['name'],
                        'description' => $stageData['description'] ?? null,
                    ]);
                }
                // Handle deletion? If a stage was in DB but not in this list...
                // Let's leave deletion to a specific Delete action to be safe, easier in UI.
            }
        }

        // Handle file attachments
        if ($request->has('pending_file_ids') && is_array($request->pending_file_ids)) {
            \App\Actions\Cloud\Files\LinkFileAction::run($project, $request->pending_file_ids);
        }

        return redirect()->route('projects.index')->with('success', 'Proyecto actualizado exitosamente.');
    }

    // Helper to delete stage individually if UI prefers
    public function destroyStage(Stage $stage)
    {
        $stage->delete();
        return back()->with('success', 'Etapa eliminada.');
    }

    public function show(Project $project, Request $request)
    {
        $project->load([
            'contact',
            'status',
            'projectType',
            'currentStage',
            'responsible',
            'team.members',
            'stages',
            'plans.team', // Simplified loading for plans list
            'files'
        ]);

        // Planner Data Loading
        $selectedPlanId = $request->query('plan_id');
        $selectedPlan = null;
        $buckets = [];

        if ($selectedPlanId) {
            $selectedPlan = \App\Models\Plan::where('project_id', $project->id)
                ->where('id', $selectedPlanId)
                ->with('team.members')
                ->first();
        }

        // If no plan selected but project has plans, maybe default to first? 
        // For now, let's keep it null unless explicitly asked, or user logic in Vue handles selection.

        if ($selectedPlan) {
            $buckets = \App\Models\Bucket::where('plan_id', $selectedPlan->id)
                ->with([
                    'tasks' => function ($query) {
                        $query->with(['assignedUsers', 'status', 'priority'])->orderBy('order');
                    }
                ])
                ->orderBy('order')
                ->get();
        }

        // Auxiliary data for Planner
        $state_type = TagCategory::where('slug', 'estado_tarea')->first();
        $priority_type = TagCategory::where('slug', 'prioridad_tarea')->first();

        $priorities = $priority_type ? (Tag::where('category_id', $priority_type->id)->get() ?? []) : [];
        $states = $state_type ? (Tag::where('category_id', $state_type->id)->get() ?? []) : [];
        $teams = \App\Services\CommonDataCacheService::getAllTeams();

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'selectedPlan' => $selectedPlan,
            'buckets' => $buckets,
            'priorities' => $priorities,
            'states' => $states,
            'teams' => $teams,
        ]);
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Proyecto eliminado.');
    }
}
