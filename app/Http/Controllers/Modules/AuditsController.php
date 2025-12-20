<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Services\AreaPermissionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuditsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $perPage = $request->get('perPage', 10);
        $activeTab = $request->get('activeTab', '');

        // Load audit types with permissions
        $auditTypes = $this->loadAuditTypes();

        // Set first available tab as active if not set
        if (!$activeTab && !empty($auditTypes)) {
            $activeTab = $auditTypes[0]['slug'];
        }

        $query = Audit::query();

        // Filter by active tab (audit type)
        if ($activeTab) {
            $query->whereHas('type', function ($q) use ($activeTab) {
                $q->where('slug', $activeTab);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('objective', 'like', '%' . $search . '%')
                    ->orWhere('place', 'like', '%' . $search . '%');
            });
        }

        $audits = $query->with(['type', 'status', 'files'])
            ->orderBy('date_audit', 'desc')
            ->paginate($perPage);

        return Inertia::render('Audits/Index', [
            'audits' => $audits,
            'auditTypes' => $auditTypes,
            'filters' => [
                'search' => $search,
                'perPage' => $perPage,
                'activeTab' => $activeTab,
            ]
        ]);
    }

    private function loadAuditTypes()
    {
        $category = TagCategory::where('slug', 'tipo_auditoria')->first();
        if (!$category) {
            return [];
        }

        $allTypes = Tag::where('category_id', $category->id)->get();

        // Filter types based on permissions
        return $allTypes->filter(function ($tag) {
            if (empty($tag->slug))
                return false;
            return AreaPermissionService::canArea('view', $tag->slug);
        })->map(function ($tag) {
            return [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            ];
        })->values()->toArray();
    }

    public function create()
    {
        $typeCategory = TagCategory::where('slug', 'tipo_auditoria')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_auditoria')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return Inertia::render('Audits/Form', [
            'audit' => null,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_register' => 'required|date',
            'date_audit' => 'required|date',
            'objective' => 'required|integer|min:0',
            'type_id' => 'nullable|exists:tags,id',
            'place' => 'required|string|max:255',
            'status_id' => 'nullable|exists:tags,id',
            'observations' => 'nullable|string',
        ]);

        $audit = Audit::create($validated);

        // Handle file attachments
        if ($request->has('pending_file_ids') && is_array($request->pending_file_ids)) {
            \App\Actions\LinkFileAction::run($audit, $request->pending_file_ids);
        }

        return redirect()->route('finances.audits.index');
    }

    public function edit(Audit $audit)
    {
        $audit->load('files');

        $typeCategory = TagCategory::where('slug', 'tipo_auditoria')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_auditoria')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return Inertia::render('Audits/Form', [
            'audit' => $audit,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function update(Request $request, Audit $audit)
    {
        $validated = $request->validate([
            'date_register' => 'required|date',
            'date_audit' => 'required|date',
            'objective' => 'required|integer|min:0',
            'type_id' => 'nullable|exists:tags,id',
            'place' => 'required|string|max:255',
            'status_id' => 'nullable|exists:tags,id',
            'observations' => 'nullable|string',
        ]);

        $audit->update($validated);

        // Handle file attachments
        if ($request->has('pending_file_ids') && is_array($request->pending_file_ids)) {
            \App\Actions\LinkFileAction::run($audit, $request->pending_file_ids);
        }

        return redirect()->route('finances.audits.index');
    }

    public function show(Audit $audit)
    {
        $audit->load(['type', 'status', 'files']);

        return Inertia::render('Audits/Show', [
            'audit' => $audit,
        ]);
    }

    public function destroy(Audit $audit)
    {
        $audit->delete();
        return redirect()->route('finances.audits.index');
    }
}
