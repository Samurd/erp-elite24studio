<?php

namespace App\Http\Controllers;

use App\Models\Sub;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;

class SubscriptionsController extends Controller
{
    public function index(Request $request)
    {
        $query = Sub::query()->with(['status', 'frequency', 'user'])->withCount(['files', 'notificationTemplates']);

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->status) {
            $query->where('status_id', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('start_date', '<=', $request->date_to);
        }

        $subs = $query->latest()->paginate(10)->withQueryString();

        $statusCategory = TagCategory::where('slug', 'estado_suscripcion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : [];

        return Inertia::render('Subs/Index', [
            'subs' => $subs,
            'statusOptions' => $statusOptions,
            'filters' => $request->only(['search', 'status', 'date_from', 'date_to']),
        ]);
    }

    public function create()
    {
        $statusCategory = TagCategory::where('slug', 'estado_suscripcion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : [];

        $frequencyCategory = TagCategory::where('slug', 'frecuencia_sub')->first();
        $frequencyOptions = $frequencyCategory ? Tag::where('category_id', $frequencyCategory->id)->get() : [];

        return Inertia::render('Subs/Form', [
            'statusOptions' => $statusOptions,
            'frequencyOptions' => $frequencyOptions,
        ]);
    }

    public function store(Request $request, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'frequency_id' => 'required|exists:tags,id',
            'type' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'status_id' => 'nullable|exists:tags,id',
            'start_date' => 'required|date',
            'renewal_date' => 'required|date',
            'notes' => 'nullable|string',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $sub = Sub::create([
            'name' => $request->name,
            'frequency_id' => $request->frequency_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'status_id' => $request->status_id,
            'start_date' => $request->start_date,
            'renewal_date' => $request->renewal_date,
            'notes' => $request->notes,
            'user_id' => Auth::id(),
        ]);

        if ($request->hasFile('files')) {
            $area = Area::where('slug', 'finanzas')->first();
            $folder = $folderMaker->execute('Suscripciones/' . $sub->name, null);
            $uploader->execute($request->file('files'), $sub, $folder->id, $area->id);
        }

        if ($request->has('pending_file_ids')) {
            $area = Area::where('slug', 'finanzas')->first();
            if ($area) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $sub, $area->id);
                    }
                }
            }
        }

        return redirect()->route('subs.index')->with('success', 'Suscripción creada correctamente');
    }

    public function edit(Sub $sub)
    {
        $sub->load('files');

        $statusCategory = TagCategory::where('slug', 'estado_suscripcion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : [];

        $frequencyCategory = TagCategory::where('slug', 'frecuencia_sub')->first();
        $frequencyOptions = $frequencyCategory ? Tag::where('category_id', $frequencyCategory->id)->get() : [];

        return Inertia::render('Subs/Form', [
            'sub' => $sub,
            'statusOptions' => $statusOptions,
            'frequencyOptions' => $frequencyOptions,
        ]);
    }

    public function update(Request $request, Sub $sub, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'frequency_id' => 'required|exists:tags,id',
            'type' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'status_id' => 'nullable|exists:tags,id',
            'start_date' => 'required|date',
            'renewal_date' => 'required|date',
            'notes' => 'nullable|string',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $sub->update([
            'name' => $request->name,
            'frequency_id' => $request->frequency_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'status_id' => $request->status_id,
            'start_date' => $request->start_date,
            'renewal_date' => $request->renewal_date,
            'notes' => $request->notes,
        ]);

        if ($request->hasFile('files')) {
            $area = Area::where('slug', 'finanzas')->first();
            $folder = $folderMaker->execute('Suscripciones/' . $sub->name, null);
            $uploader->execute($request->file('files'), $sub, $folder->id, $area->id);
        }

        if ($request->has('pending_file_ids')) {
            $area = Area::where('slug', 'finanzas')->first();
            if ($area) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $sub, $area->id);
                    }
                }
            }
        }

        return redirect()->route('subs.index')->with('success', 'Suscripción actualizada correctamente');
    }

    public function createNotification(Sub $sub)
    {
        if ($sub->renewal_date && $sub->user_id) {
            $sub->createAutomaticNotification();
            return back()->with('success', 'Notificación creada correctamente.');
        }
        return back()->with('error', 'Faltan datos para crear la notificación.');
    }

    public function destroy(Sub $sub)
    {
        $sub->delete();
        return redirect()->route('subs.index')->with('success', 'Suscripción eliminada correctamente');
    }
}
