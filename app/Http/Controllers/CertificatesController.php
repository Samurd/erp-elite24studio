<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;

class CertificatesController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificate::query()->with(['type', 'status', 'assignedTo', 'files']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->type_filter) {
            $query->where('type_id', $request->type_filter);
        }

        if ($request->status_filter) {
            $query->where('status_id', $request->status_filter);
        }

        if ($request->assigned_to_filter) {
            $query->where('assigned_to_id', $request->assigned_to_filter);
        }

        if ($request->date_from) {
            $query->where('issued_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('issued_at', '<=', $request->date_to);
        }

        $certificates = $query->latest()->paginate(10)->withQueryString();

        $typeCategory = TagCategory::where('slug', 'tipo_certificado')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : [];

        $statusCategory = TagCategory::where('slug', 'estado_certificado')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : [];

        $userOptions = \App\Services\CommonDataCacheService::getAllUsers();

        return Inertia::render('Certificates/Index', [
            'certificates' => $certificates,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'userOptions' => $userOptions,
            'filters' => $request->only(['search', 'type_filter', 'status_filter', 'assigned_to_filter', 'date_from', 'date_to']),
        ]);
    }

    public function create()
    {
        $typeCategory = TagCategory::where('slug', 'tipo_certificado')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : [];

        $statusCategory = TagCategory::where('slug', 'estado_certificado')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : [];

        $userOptions = \App\Services\CommonDataCacheService::getAllUsers();

        return Inertia::render('Certificates/Form', [
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'userOptions' => $userOptions,
        ]);
    }

    public function store(Request $request, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:tags,id',
            'status_id' => 'nullable|exists:tags,id',
            'assigned_to_id' => 'nullable|exists:users,id',
            'issued_at' => 'required|date',
            'expires_at' => 'nullable|date',
            'description' => 'nullable|string',
            'description' => 'nullable|string',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $certificate = Certificate::create([
            'name' => $request->name,
            'type_id' => $request->type_id,
            'status_id' => $request->status_id,
            'assigned_to_id' => $request->assigned_to_id,
            'issued_at' => $request->issued_at,
            'expires_at' => $request->expires_at,
            'description' => $request->description,
        ]);

        if ($request->hasFile('files')) {
            $area = Area::where('slug', 'certificados')->first();
            $folder = $folderMaker->execute('Certificados/' . $certificate->name, null);
            $uploader->execute($request->file('files'), $certificate, $folder->id, $area->id);
        }

        if ($request->has('pending_file_ids')) {
            $area = Area::where('slug', 'certificados')->first();
            if ($area) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $certificate, $area->id);
                    }
                }
            }
        }

        return redirect()->route('certificates.index')->with('success', 'Certificado creado correctamente');
    }

    public function show(Certificate $certificate)
    {
        $certificate->load(['type', 'status', 'assignedTo', 'files.user']);

        return Inertia::render('Certificates/Show', [
            'certificate' => $certificate,
        ]);
    }

    public function edit(Certificate $certificate)
    {
        $certificate->load('files');

        $typeCategory = TagCategory::where('slug', 'tipo_certificado')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : [];

        $statusCategory = TagCategory::where('slug', 'estado_certificado')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : [];

        $userOptions = \App\Services\CommonDataCacheService::getAllUsers();

        return Inertia::render('Certificates/Form', [
            'certificate' => $certificate,
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'userOptions' => $userOptions,
        ]);
    }

    public function update(Request $request, Certificate $certificate, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:tags,id',
            'status_id' => 'nullable|exists:tags,id',
            'assigned_to_id' => 'nullable|exists:users,id',
            'issued_at' => 'required|date',
            'expires_at' => 'nullable|date',
            'description' => 'nullable|string',
            'description' => 'nullable|string',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $certificate->update([
            'name' => $request->name,
            'type_id' => $request->type_id,
            'status_id' => $request->status_id,
            'assigned_to_id' => $request->assigned_to_id,
            'issued_at' => $request->issued_at,
            'expires_at' => $request->expires_at,
            'description' => $request->description,
        ]);

        if ($request->hasFile('files')) {
            $area = Area::where('slug', 'certificados')->first();
            $folder = $folderMaker->execute('Certificados/' . $certificate->name, null);
            $uploader->execute($request->file('files'), $certificate, $folder->id, $area->id);
        }

        if ($request->has('pending_file_ids')) {
            $area = Area::where('slug', 'certificados')->first();
            if ($area) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $certificate, $area->id);
                    }
                }
            }
        }

        return redirect()->route('certificates.index')->with('success', 'Certificado actualizado correctamente');
    }

    public function destroy(Certificate $certificate)
    {
        $certificate->delete();
        return redirect()->route('certificates.index')->with('success', 'Certificado eliminado correctamente');
    }
}
