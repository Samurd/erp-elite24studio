<?php

namespace App\Http\Controllers;

use App\Models\License;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Project;
use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;

class LicensesController extends Controller
{
    public function index(Request $request)
    {
        $query = License::with(['project', 'type', 'status', 'files']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('entity', 'like', "%{$request->search}%")
                    ->orWhere('company', 'like', "%{$request->search}%")
                    ->orWhere('eradicated_number', 'like', "%{$request->search}%");
            });
        }

        if ($request->license_type_filter) {
            $query->where('license_type_id', $request->license_type_filter);
        }

        if ($request->status_filter) {
            $query->where('status_id', $request->status_filter);
        }

        if ($request->entity_filter) {
            $query->where('entity', 'like', "%{$request->entity_filter}%");
        }

        if ($request->company_filter) {
            $query->where('company', 'like', "%{$request->company_filter}%");
        }

        if ($request->requires_extension_filter !== null) {
            $query->where('requires_extension', $request->requires_extension_filter);
        }

        if ($request->date_from) {
            $query->where('expiration_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('expiration_date', '<=', $request->date_to);
        }

        $licenses = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $licenseTypeCategory = TagCategory::where('slug', 'tipo_licencia')->first();
        $licenseTypeOptions = $licenseTypeCategory ? Tag::where('category_id', $licenseTypeCategory->id)->get() : [];

        $statusCategory = TagCategory::where('slug', 'estado_licencia')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : [];

        return Inertia::render('Licenses/Index', [
            'licenses' => $licenses,
            'licenseTypeOptions' => $licenseTypeOptions,
            'statusOptions' => $statusOptions,
            'filters' => $request->only(['search', 'license_type_filter', 'status_filter', 'entity_filter', 'company_filter', 'requires_extension_filter', 'date_from', 'date_to']),
        ]);
    }

    public function create()
    {
        $licenseTypeCategory = TagCategory::where('slug', 'tipo_licencia')->first();
        $licenseTypeOptions = $licenseTypeCategory ? Tag::where('category_id', $licenseTypeCategory->id)->get() : [];

        $statusCategory = TagCategory::where('slug', 'estado_licencia')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : [];

        $projects = Project::orderBy('name')->get();

        return Inertia::render('Licenses/Form', [
            'licenseTypeOptions' => $licenseTypeOptions,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
        ]);
    }

    public function store(Request $request, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'license_type_id' => 'required|exists:tags,id',
            'status_id' => 'nullable|exists:tags,id',
            'entity' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'eradicated_number' => 'nullable|string|max:255',
            'eradicatd_date' => 'nullable|date',
            'estimated_approval_date' => 'nullable|date',
            'expiration_date' => 'nullable|date',
            'requires_extension' => 'boolean',
            'observations' => 'nullable|string',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $license = License::create([
            'project_id' => $request->project_id,
            'license_type_id' => $request->license_type_id,
            'status_id' => $request->status_id,
            'entity' => $request->entity,
            'company' => $request->company,
            'eradicated_number' => $request->eradicated_number,
            'eradicatd_date' => $request->eradicatd_date,
            'estimated_approval_date' => $request->estimated_approval_date,
            'expiration_date' => $request->expiration_date,
            'requires_extension' => $request->requires_extension,
            'observations' => $request->observations,
        ]);

        if ($request->hasFile('files')) {
            $area = Area::where('slug', 'licencias')->first();
            $projectName = $license->project ? $license->project->name : 'Sin Proyecto';
            $folderName = "Licencia " . $license->id . " - " . $projectName;

            $folder = $folderMaker->execute('Licencias/' . $folderName, null);
            $uploader->execute($request->file('files'), $license, $folder->id, $area->id);
        }

        if ($request->has('pending_file_ids')) {
            $areaId = Area::where('slug', 'licencias')->value('id');
            if ($areaId) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $license, $areaId);
                    }
                }
            }
        }

        return redirect()->route('licenses.index')->with('success', 'Licencia creada correctamente');
    }

    public function edit(License $license)
    {
        $license->load('files');

        $licenseTypeCategory = TagCategory::where('slug', 'tipo_licencia')->first();
        $licenseTypeOptions = $licenseTypeCategory ? Tag::where('category_id', $licenseTypeCategory->id)->get() : [];

        $statusCategory = TagCategory::where('slug', 'estado_licencia')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : [];

        $projects = Project::orderBy('name')->get();

        return Inertia::render('Licenses/Form', [
            'license' => $license,
            'licenseTypeOptions' => $licenseTypeOptions,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
        ]);
    }

    public function update(Request $request, License $license, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'license_type_id' => 'required|exists:tags,id',
            'status_id' => 'nullable|exists:tags,id',
            'entity' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'eradicated_number' => 'nullable|string|max:255',
            'eradicatd_date' => 'nullable|date',
            'estimated_approval_date' => 'nullable|date',
            'expiration_date' => 'nullable|date',
            'requires_extension' => 'boolean',
            'observations' => 'nullable|string',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $license->update([
            'project_id' => $request->project_id,
            'license_type_id' => $request->license_type_id,
            'status_id' => $request->status_id,
            'entity' => $request->entity,
            'company' => $request->company,
            'eradicated_number' => $request->eradicated_number,
            'eradicatd_date' => $request->eradicatd_date,
            'estimated_approval_date' => $request->estimated_approval_date,
            'expiration_date' => $request->expiration_date,
            'requires_extension' => $request->requires_extension,
            'observations' => $request->observations,
        ]);

        if ($request->hasFile('files')) {
            $area = Area::where('slug', 'licencias')->first();
            $projectName = $license->project ? $license->project->name : 'Sin Proyecto';
            $folderName = "Licencia " . $license->id . " - " . $projectName;

            $folder = $folderMaker->execute('Licencias/' . $folderName, null);
            $uploader->execute($request->file('files'), $license, $folder->id, $area->id);
        }

        if ($request->has('pending_file_ids')) {
            $areaId = Area::where('slug', 'licencias')->value('id');
            if ($areaId) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $license, $areaId);
                    }
                }
            }
        }

        return redirect()->route('licenses.index')->with('success', 'Licencia actualizada correctamente');
    }

    public function destroy(License $license)
    {
        $license->delete();
        return redirect()->route('licenses.index')->with('success', 'Licencia eliminada correctamente');
    }
}
