<?php

namespace App\Http\Controllers;

use App\Models\CaseRecord;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;

class CaseRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = CaseRecord::query()->with(['contact', 'status', 'assignedTo', 'type']);

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->orWhereHas('assignedTo', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
                $q->orWhereHas('contact', function ($contactQuery) use ($search) {
                    $contactQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            });
        }

        if ($request->canal) {
            $query->where('channel', 'like', "%{$request->canal}%");
        }

        if ($request->estado) {
            $query->where('status_id', $request->estado);
        }

        if ($request->asesor) {
            $query->where('assigned_to_id', $request->asesor);
        }

        if ($request->tipo_caso) {
            $query->where('case_type_id', $request->tipo_caso);
        }

        if ($request->fecha) {
            $query->where('date', $request->fecha);
        }

        $records = $query->latest()->paginate(10)->withQueryString();

        // Filters Data
        $state = TagCategory::where('slug', 'estado_caso')->first();
        $case_type = TagCategory::where('slug', 'tipo_caso')->first();
        $states = $state ? Tag::where('category_id', $state->id)->get() : [];
        $case_types = $case_type ? Tag::where('category_id', $case_type->id)->get() : [];
        $users = \App\Services\CommonDataCacheService::getAllUsers();

        return Inertia::render('CaseRecord/Index', [
            'records' => $records,
            'states' => $states,
            'case_types' => $case_types,
            'users' => $users,
            'filters' => $request->only(['search', 'canal', 'estado', 'asesor', 'tipo_caso', 'fecha']),
        ]);
    }

    public function create()
    {
        $contacts = Contact::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        $state = TagCategory::where('slug', 'estado_caso')->first();
        $case_type = TagCategory::where('slug', 'tipo_caso')->first();

        $states = $state ? Tag::where('category_id', $state->id)->get() : [];
        $case_types = $case_type ? Tag::where('category_id', $case_type->id)->get() : [];

        return Inertia::render('CaseRecord/Form', [
            'contacts' => $contacts,
            'users' => $users,
            'states' => $states,
            'case_types' => $case_types,
            'defaultUserId' => Auth::id(),
            'currentDate' => date('Y-m-d'),
        ]);
    }

    public function store(Request $request, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $request->validate([
            'date' => 'required|date',
            'channel' => 'required|string|max:255',
            'case_type_id' => 'required|exists:tags,id',
            'status_id' => 'required|exists:tags,id',
            'assigned_to_id' => 'required|exists:users,id',
            'contact_id' => 'required|exists:contacts,id',
            'description' => 'nullable|string',
            'files.*' => 'file|max:51200', // 50MB
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $record = CaseRecord::create([
            'date' => $request->date,
            'channel' => $request->channel,
            'case_type_id' => $request->case_type_id,
            'status_id' => $request->status_id,
            'assigned_to_id' => $request->assigned_to_id,
            'contact_id' => $request->contact_id,
            'description' => $request->description,
        ]);

        if ($request->hasFile('files')) {
            $area = Area::where('slug', 'registro-casos')->first();
            $folder = $folderMaker->execute("Caso-" . $record->id, null); // Or specific naming convention
            $uploader->execute($request->file('files'), $record, $folder->id, $area->id);
        }

        if ($request->has('pending_file_ids')) {
            $area = Area::where('slug', 'registro-casos')->first();
            foreach ($request->pending_file_ids as $fileId) {
                $file = \App\Models\File::find($fileId);
                if ($file) {
                    $linker->execute($file, $record, $area->id);
                }
            }
        }

        return redirect()->route('case-record.index')->with('success', 'Registro creado correctamente');
    }

    public function edit(CaseRecord $caseRecord)
    {
        $caseRecord->load(['files']); // Load attached files

        $contacts = Contact::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        $state = TagCategory::where('slug', 'estado_caso')->first();
        $case_type = TagCategory::where('slug', 'tipo_caso')->first();

        $states = $state ? Tag::where('category_id', $state->id)->get() : [];
        $case_types = $case_type ? Tag::where('category_id', $case_type->id)->get() : [];

        return Inertia::render('CaseRecord/Form', [
            'caseRecord' => $caseRecord,
            'contacts' => $contacts,
            'users' => $users,
            'states' => $states,
            'case_types' => $case_types,
        ]);
    }

    public function update(Request $request, CaseRecord $caseRecord, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker)
    {
        $request->validate([
            'date' => 'required|date',
            'channel' => 'required|string|max:255',
            'case_type_id' => 'required|exists:tags,id',
            'status_id' => 'required|exists:tags,id',
            'assigned_to_id' => 'required|exists:users,id',
            'contact_id' => 'required|exists:contacts,id',
            'description' => 'nullable|string',
            'files.*' => 'file|max:51200',
        ]);

        $caseRecord->update([
            'date' => $request->date,
            'channel' => $request->channel,
            'case_type_id' => $request->case_type_id,
            'status_id' => $request->status_id,
            'assigned_to_id' => $request->assigned_to_id,
            'contact_id' => $request->contact_id,
            'description' => $request->description,
        ]);

        if ($request->hasFile('files')) {
            $area = Area::where('slug', 'registro-casos')->first();
            $folder = $folderMaker->execute("Caso-" . $caseRecord->id, null);
            $uploader->execute($request->file('files'), $caseRecord, $folder->id, $area->id);
        }

        return redirect()->route('case-record.index')->with('success', 'Registro actualizado correctamente');
    }

    public function destroy(CaseRecord $caseRecord)
    {
        $caseRecord->delete();
        return redirect()->route('case-record.index')->with('success', 'Registro eliminado correctamente');
    }
}
