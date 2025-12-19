<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::query()->with(['status', 'user'])->withCount('files');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status_id', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        $reports = $query->latest()->paginate(10)->withQueryString();

        $statusCategory = TagCategory::where('slug', 'estado_reporte')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : [];

        return Inertia::render('Reports/Index', [
            'reports' => $reports,
            'statusOptions' => $statusOptions,
            'filters' => $request->only(['search', 'status', 'date_from', 'date_to']),
        ]);
    }

    public function create()
    {
        $statusCategory = TagCategory::where('slug', 'estado_reporte')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : [];

        return Inertia::render('Reports/Form', [
            'statusOptions' => $statusOptions,
        ]);
    }

    public function store(Request $request, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'hour' => 'nullable|date_format:H:i',
            'status_id' => 'required|exists:tags,id',
            'notes' => 'nullable|string',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $report = Report::create([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'hour' => $request->hour,
            'status_id' => $request->status_id,
            'notes' => $request->notes,
            'user_id' => Auth::id(),
        ]);

        if ($request->hasFile('files')) {
            $area = Area::where('slug', 'reportes')->first();
            $folder = $folderMaker->execute('Reportes/' . $report->title, null);
            $uploader->execute($request->file('files'), $report, $folder->id, $area->id);
        }

        if ($request->has('pending_file_ids')) {
            $area = Area::where('slug', 'reportes')->first();
            foreach ($request->pending_file_ids as $fileId) {
                $file = \App\Models\File::find($fileId);
                if ($file) {
                    $linker->execute($file, $report, $area->id);
                }
            }
        }

        return redirect()->route('reports.index')->with('success', 'Reporte creado correctamente');
    }

    public function edit(Report $report)
    {
        $report->load('files');

        $statusCategory = TagCategory::where('slug', 'estado_reporte')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : [];

        return Inertia::render('Reports/Form', [
            'report' => $report,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function update(Request $request, Report $report, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'hour' => 'nullable|date_format:H:i', // Format H:i for time inputs
            'status_id' => 'required|exists:tags,id',
            'notes' => 'nullable|string',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $report->update([
            'title' => $request->title,
            'description' => $request->description,
            'date' => $request->date,
            'hour' => $request->hour,
            'status_id' => $request->status_id,
            'notes' => $request->notes,
        ]);

        if ($request->hasFile('files')) {
            $area = Area::where('slug', 'reportes')->first();
            $folder = $folderMaker->execute('Reportes/' . $report->title, null);
            $uploader->execute($request->file('files'), $report, $folder->id, $area->id);
        }

        if ($request->has('pending_file_ids')) {
            $area = Area::where('slug', 'reportes')->first();
            foreach ($request->pending_file_ids as $fileId) {
                $file = \App\Models\File::find($fileId);
                if ($file) {
                    $linker->execute($file, $report, $area->id);
                }
            }
        }

        return redirect()->route('reports.index')->with('success', 'Reporte actualizado correctamente');
    }

    public function destroy(Report $report)
    {
        $report->delete();
        return redirect()->route('reports.index')->with('success', 'Reporte eliminado correctamente');
    }
}
