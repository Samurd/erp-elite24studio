<?php

namespace App\Http\Controllers\Modules\Marketing;

use App\Http\Controllers\Controller;
use App\Models\CaseMarketing;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use App\Models\Project;
use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;

class CasesController extends Controller
{
    public function index(Request $request)
    {
        $query = CaseMarketing::with(['project', 'type', 'status', 'responsible']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Filters
        if ($request->filled('type_filter')) {
            $query->where('type_id', $request->type_filter);
        }

        if ($request->filled('status_filter')) {
            $query->where('status_id', $request->status_filter);
        }

        if ($request->filled('project_filter')) {
            $query->where('project_id', $request->project_filter);
        }

        if ($request->filled('responsible_filter')) {
            $query->where('responsible_id', $request->responsible_filter);
        }

        if ($request->filled('mediums_filter')) {
            $query->where('mediums', 'like', '%' . $request->mediums_filter . '%');
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $cases = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('perPage', 10))
            ->withQueryString();

        // Options
        $typeCategory = TagCategory::where('slug', 'tipo_caso_mk')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_caso_mk')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return Inertia::render('Modules/Marketing/Cases/Index', [
            'cases' => $cases,
            'filters' => $request->only([
                'search',
                'type_filter',
                'status_filter',
                'project_filter',
                'responsible_filter',
                'mediums_filter',
                'date_from',
                'date_to',
                'perPage'
            ]),
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'users' => $users,
        ]);
    }

    public function create()
    {
        $typeCategory = TagCategory::where('slug', 'tipo_caso_mk')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_caso_mk')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return Inertia::render('Modules/Marketing/Cases/Form', [
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'users' => $users,
        ]);
    }

    public function store(Request $request, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'date' => 'required|date',
            'mediums' => 'required|string',
            'description' => 'nullable|string',
            'responsible_id' => 'nullable|exists:users,id',
            'type_id' => 'nullable|exists:tags,id',
            'type_id' => 'nullable|exists:tags,id',
            'status_id' => 'nullable|exists:tags,id',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $case = CaseMarketing::create($validated);

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            // Obtener ID del área de marketing
            $areaId = Area::where('slug', 'marketing')->value('id');

            $folderId = null;
            if ($case->subject) {
                $folder = $folderMaker->execute($case->subject, null);
                $folderId = $folder->id;
            }

            $uploader->execute($files, $case, $folderId, $areaId);
        }

        if ($request->has('pending_file_ids')) {
            $areaId = Area::where('slug', 'marketing')->value('id');
            if ($areaId) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $case, $areaId);
                    }
                }
            }
        }

        return redirect()->route('marketing.cases.index')->with('success', 'Caso creado exitosamente.');
    }

    public function edit(CaseMarketing $caseMarketing)
    {
        $typeCategory = TagCategory::where('slug', 'tipo_caso_mk')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_caso_mk')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        $caseMarketing->load('files');

        return Inertia::render('Modules/Marketing/Cases/Form', [
            'caseMarketing' => $caseMarketing,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'users' => $users,
        ]);
    }

    public function update(Request $request, CaseMarketing $caseMarketing, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'project_id' => 'nullable|exists:projects,id',
            'date' => 'required|date',
            'mediums' => 'required|string',
            'description' => 'nullable|string',
            'responsible_id' => 'nullable|exists:users,id',
            'type_id' => 'nullable|exists:tags,id',
            'status_id' => 'nullable|exists:tags,id',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $caseMarketing->update($validated);

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            // Obtener ID del área de marketing
            $areaId = Area::where('slug', 'marketing')->value('id');

            $folderId = null;
            if ($caseMarketing->subject) {
                $folder = $folderMaker->execute($caseMarketing->subject, null);
                $folderId = $folder->id;
            }

            $uploader->execute($files, $caseMarketing, $folderId, $areaId);
        }

        if ($request->has('pending_file_ids')) {
            $areaId = Area::where('slug', 'marketing')->value('id');
            if ($areaId) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $caseMarketing, $areaId);
                    }
                }
            }
        }

        return redirect()->route('marketing.cases.index')->with('success', 'Caso actualizado exitosamente.');
    }

    public function destroy(CaseMarketing $caseMarketing)
    {
        $caseMarketing->delete();
        return redirect()->route('marketing.cases.index')->with('success', 'Caso eliminado exitosamente.');
    }
}
