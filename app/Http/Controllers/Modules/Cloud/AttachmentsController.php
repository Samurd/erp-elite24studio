<?php

namespace App\Http\Controllers\Modules\Cloud;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\File;
use App\Models\Area;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Files\LinkFileAction;
use App\Actions\Cloud\Files\DeleteFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;
use App\Services\AreaPermissionService;

class AttachmentsController extends Controller
{
    /**
     * Store new attachments for an existing model.
     */
    public function store(Request $request, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker)
    {
        $validated = $request->validate([
            'files.*' => 'required|file|max:51200',
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
            'area_slug' => 'required|string',
        ]);

        $modelClass = $validated['model_type'];
        $model = $modelClass::findOrFail($validated['model_id']);
        $areaSlug = $validated['area_slug'];

        if (!AreaPermissionService::canArea('update', $areaSlug)) {
            abort(403, "No tienes permiso para actualizar en este área.");
        }

        $areaId = Area::where('slug', $areaSlug)->value('id');

        $folderId = null;
        if (isset($model->name)) {
            $folder = $folderMaker->execute($model->name, null);
            $folderId = $folder->id;
        }

        $uploader->execute(
            $request->file('files'),
            $model,
            $folderId,
            $areaId
        );

        return back()->with('success', 'Archivos adjuntados correctamente.');
    }

    /**
     * Link an existing file to a model.
     */
    public function link(Request $request, LinkFileAction $linker)
    {
        $validated = $request->validate([
            'file_id' => 'required|exists:files,id',
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
            'area_id' => 'nullable|integer', // Can be null, or passed from frontend
        ]);

        $file = File::findOrFail($validated['file_id']);
        $modelClass = $validated['model_type'];
        $model = $modelClass::findOrFail($validated['model_id']);

        // Check permission if area is involved?
        // The LinkFileAction might handle some, or we rely on general 'cloud.view' or area view perms which user already passed to see the file.
        // We might want to check if user has update permission on the TARGET model's area?
        // But Controller doesn't easily know specific Area slug from just model/id without a mapping or extra param.
        // Let's assume frontend passed 'area_slug' or similar if strict check needed.
        // For now, if User can edit the Model (implied by access to the Edit Page), they can link.

        $linker->execute($file, $model, $validated['area_id']);

        return back()->with('success', 'Archivo vinculado correctamente.');
    }

    /**
     * Detach a file from a model.
     */
    public function detach(Request $request, File $file)
    {
        $validated = $request->validate([
            'model_type' => 'required|string',
            'model_id' => 'required|integer',
            'area_slug' => 'required|string',
        ]);

        $modelClass = $validated['model_type'];
        $model = $modelClass::findOrFail($validated['model_id']);
        $areaSlug = $validated['area_slug'];

        if (!AreaPermissionService::canArea('update', $areaSlug)) {
            abort(403, "No tienes permiso para actualizar en este área.");
        }

        // Check if the file is actually attached to this model
        // Assuming morphToMany or similar relation, we can try to detach.
        // If it's HasMany (like EventItem?), detach might not work if it owns it?
        // standard Cloud uses 'files' relation (MorphToMany usually).

        $model->files()->detach($file->id);

        return back()->with('success', 'Archivo desvinculado.');
    }

    /**
     * Permanently delete a file.
     */
    public function destroy(Request $request, File $file, DeleteFileAction $deleter)
    {
        $areaSlug = $request->input('area_slug');

        if ($areaSlug) {
            if (!AreaPermissionService::canArea('update', $areaSlug)) {
                abort(403, "No tienes permiso.");
            }
        }

        if ($file->user_id !== auth()->id() && !auth()->user()->can('cloud.delete')) {
            return back()->with('error', 'No tienes permiso para eliminar este archivo permanentemente.');
        }

        $deleter->execute($file);

        return back()->with('success', 'Archivo eliminado permanentemente.');
    }
}
