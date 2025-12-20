<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Worksite;
use App\Models\Project;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use App\Models\PunchItem;
use App\Models\Change;
use App\Models\Visit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class WorksitesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $typeFilter = $request->get('type_filter', '');
        $statusFilter = $request->get('status_filter', '');
        $projectFilter = $request->get('project_filter', '');
        $responsibleFilter = $request->get('responsible_filter', '');
        $dateFrom = $request->get('date_from', '');
        $dateTo = $request->get('date_to', '');
        $perPage = $request->get('perPage', 10);

        $query = Worksite::with([
            'project',
            'type',
            'status',
            'responsible'
        ]);

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%');
            });
        }

        // Filters
        if ($typeFilter)
            $query->where('type_id', $typeFilter);
        if ($statusFilter)
            $query->where('status_id', $statusFilter);
        if ($projectFilter)
            $query->where('project_id', $projectFilter);
        if ($responsibleFilter)
            $query->where('responsible_id', $responsibleFilter);

        // Date Range
        if ($dateFrom)
            $query->whereDate('start_date', '>=', $dateFrom);
        if ($dateTo)
            $query->whereDate('end_date', '<=', $dateTo);

        $worksites = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Options
        $typeCategory = TagCategory::where('slug', 'tipo_obra')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_obra')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $projects = \App\Services\CommonDataCacheService::getAllContacts(); // Actually caching contacts? Index.php said getAllContacts which seems wrong if it displays Projects. Let's double check Index.php. It says getAllContacts assigned to $projects. That might be a bug or misnaming in existing code. Wait, `Project::orderBy('name')->get()` was used in Create.php. Let's use `Project::orderBy('name')->get()` to be safe.
        // Correction: In Index.php line 153: $projects = \App\Services\CommonDataCacheService::getAllContacts();
        // But in Create.php line 33: $projects = Project::orderBy('name')->get();
        // Since filtering by project ID usually requires Projects, I'll fetch Projects.
        $projects = Project::orderBy('name')->select('id', 'name')->get();

        $responsibles = \App\Services\CommonDataCacheService::getAllUsers();

        return Inertia::render('Worksites/Index', [
            'worksites' => $worksites,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'projectOptions' => $projects,
            'responsibleOptions' => $responsibles,
            'filters' => [
                'search' => $search,
                'type_filter' => $typeFilter,
                'status_filter' => $statusFilter,
                'project_filter' => $projectFilter,
                'responsible_filter' => $responsibleFilter,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'perPage' => $perPage,
            ]
        ]);
    }

    public function create()
    {
        $typeCategory = TagCategory::where('slug', 'tipo_obra')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_obra')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return Inertia::render('Worksites/Form', [
            'worksite' => null,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'type_id' => 'nullable|exists:tags,id',
            'status_id' => 'nullable|exists:tags,id',
            'responsible_id' => 'nullable|exists:users,id',
            'address' => 'nullable|string|max:500',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        Worksite::create($validated);

        return redirect()->route('worksites.index')->with('success', 'Obra creada exitosamente.');
    }

    public function edit(Worksite $worksite)
    {
        $typeCategory = TagCategory::where('slug', 'tipo_obra')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_obra')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return Inertia::render('Worksites/Form', [
            'worksite' => $worksite,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'users' => $users,
        ]);
    }

    public function update(Request $request, Worksite $worksite)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'type_id' => 'nullable|exists:tags,id',
            'status_id' => 'nullable|exists:tags,id',
            'responsible_id' => 'nullable|exists:users,id',
            'address' => 'nullable|string|max:500',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $worksite->update($validated);

        return redirect()->route('worksites.index')->with('success', 'Obra actualizada exitosamente.');
    }

    public function show(Request $request, Worksite $worksite)
    {
        $worksite->load(['project', 'type', 'status', 'responsible']);

        // --- Punch Items ---
        $punchSearch = $request->get('punch_search', '');
        $statusFilter = $request->get('status_filter', ''); // Punch Status
        $responsibleFilter = $request->get('responsible_filter', ''); // Punch Responsible
        $punchPerPage = $request->get('punch_perPage', 10);

        $punchQuery = PunchItem::with(['responsible', 'files'])
            ->where('worksite_id', $worksite->id);

        if ($punchSearch) {
            $punchQuery->where('observations', 'like', '%' . $punchSearch . '%');
        }
        if ($statusFilter) {
            $punchQuery->where('status_id', $statusFilter);
        }
        if ($responsibleFilter) {
            $punchQuery->where('responsible_id', $responsibleFilter);
        }
        $punchItems = $punchQuery->orderBy('created_at', 'desc')->paginate($punchPerPage, ['*'], 'punch_page');


        // --- Changes ---
        $changeSearch = $request->get('change_search', '');
        $changeTypeFilter = $request->get('change_type_filter', '');
        $changeStatusFilter = $request->get('change_status_filter', '');
        $changePerPage = $request->get('change_perPage', 10);

        $changeQuery = Change::with(['type', 'status', 'budgetImpact', 'approver', 'files'])
            ->where('worksite_id', $worksite->id);

        if ($changeSearch) {
            $changeQuery->where('description', 'like', '%' . $changeSearch . '%');
        }
        if ($changeTypeFilter) {
            $changeQuery->where('change_type_id', $changeTypeFilter);
        }
        if ($changeStatusFilter) {
            $changeQuery->where('status_id', $changeStatusFilter);
        }
        $changes = $changeQuery->orderBy('change_date', 'desc')->paginate($changePerPage, ['*'], 'change_page');


        // --- Visits ---
        $visitSearch = $request->get('visit_search', '');
        $visitStatusFilter = $request->get('visit_status_filter', '');
        $visitVisitorFilter = $request->get('visit_visitor_filter', '');
        $visitPerPage = $request->get('visit_perPage', 10);

        $visitQuery = Visit::with(['visitor', 'performer', 'status', 'files'])
            ->where('worksite_id', $worksite->id);

        if ($visitSearch) {
            $visitQuery->where('general_observations', 'like', '%' . $visitSearch . '%');
        }
        if ($visitStatusFilter) {
            $visitQuery->where('status_id', $visitStatusFilter);
        }
        if ($visitVisitorFilter) {
            $visitQuery->where('performed_by', $visitVisitorFilter);
        }
        $visits = $visitQuery->orderBy('visit_date', 'desc')->paginate($visitPerPage, ['*'], 'visit_page');

        // --- Common Data ---
        $users = User::orderBy('name')->select('id', 'name')->get();

        $punchStatusCategory = TagCategory::where('slug', 'estado_punch_item')->first();
        $punchStatusOptions = $punchStatusCategory ? Tag::where('category_id', $punchStatusCategory->id)->get() : collect();

        $changeTypeCategory = TagCategory::where('slug', 'tipo_cambio')->first();
        $changeTypeOptions = $changeTypeCategory ? Tag::where('category_id', $changeTypeCategory->id)->get() : collect();
        $changeStatusCategory = TagCategory::where('slug', 'estado_cambio')->first();
        $changeStatusOptions = $changeStatusCategory ? Tag::where('category_id', $changeStatusCategory->id)->get() : collect();

        $visitStatusCategory = TagCategory::where('slug', 'estado_visita')->first();
        $visitStatusOptions = $visitStatusCategory ? Tag::where('category_id', $visitStatusCategory->id)->get() : collect();

        return Inertia::render('Worksites/Show', [
            'worksite' => $worksite,
            'users' => $users,
            'punchItems' => $punchItems,
            'changes' => $changes,
            'visits' => $visits,
            'punchStatusOptions' => $punchStatusOptions,
            'changeTypeOptions' => $changeTypeOptions,
            'changeStatusOptions' => $changeStatusOptions,
            'visitStatusOptions' => $visitStatusOptions,

            // Sub-filters for state persistence
            'punchFilters' => [
                'punch_search' => $punchSearch,
                'status_filter' => $statusFilter,
                'responsible_filter' => $responsibleFilter,
                'punch_perPage' => $punchPerPage
            ],
            'changeFilters' => [
                'change_search' => $changeSearch,
                'change_type_filter' => $changeTypeFilter,
                'change_status_filter' => $changeStatusFilter,
                'change_perPage' => $changePerPage
            ],
            'visitFilters' => [
                'visit_search' => $visitSearch,
                'visit_status_filter' => $visitStatusFilter,
                'visit_visitor_filter' => $visitVisitorFilter,
                'visit_perPage' => $visitPerPage
            ]
        ]);
    }

    public function destroy(Worksite $worksite)
    {
        $worksite->delete();
        return redirect()->route('worksites.index')->with('success', 'Obra eliminada exitosamente.');
    }

    public function destroyPunchItem(PunchItem $punchItem)
    {
        $punchItem->delete();
        return back()->with('success', 'Punch Item eliminado.');
    }

    public function destroyChange(Change $change)
    {
        $change->delete();
        return back()->with('success', 'Cambio eliminado.');
    }

    public function destroyVisit(Visit $visit)
    {
        $visit->delete();
        return back()->with('success', 'Visita eliminada.');
    }
}
