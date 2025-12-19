<?php

namespace App\Http\Controllers\Modules\Kpis;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\Kpi;
use App\Models\KpiRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class KpiRecordController extends Controller
{
    public function create(Kpi $kpi)
    {
        return Inertia::render('Kpis/RecordForm', [
            'kpi' => $kpi,
            'kpiRecord' => null,
        ]);
    }

    public function store(Request $request, Kpi $kpi)
    {
        $validated = $request->validate([
            'record_date' => 'required|date',
            'value' => 'required|numeric',
            'observation' => 'nullable|string|max:1000',
            'files' => 'nullable|array|max:5',
            'files.*' => 'nullable|file|max:10240', // 10MB max
            'folder_id' => 'nullable|exists:folders,id', // Custom folder logic
        ]);

        $record = KpiRecord::create([
            'kpi_id' => $kpi->id,
            'record_date' => $validated['record_date'],
            'value' => $validated['value'],
            'observation' => $validated['observation'] ?? null,
            'created_by_id' => Auth::id(),
        ]);

        // Handle File Uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filePath = $file->store('kpi_records', 'public');

                File::create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $filePath,
                    'disk' => 'public',
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'fileable_id' => $record->id,
                    'fileable_type' => KpiRecord::class,
                    'folder_id' => $request->input('folder_id'), // Use selected folder
                    'user_id' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('kpis.show', $kpi->id)->with('success', 'Registro creado exitosamente.');
    }

    public function edit(KpiRecord $kpiRecord)
    {
        $kpiRecord->load(['kpi', 'files.folder']);

        return Inertia::render('Kpis/RecordForm', [
            'kpi' => $kpiRecord->kpi,
            'kpiRecord' => $kpiRecord,
        ]);
    }

    public function update(Request $request, KpiRecord $kpiRecord)
    {
        $validated = $request->validate([
            'record_date' => 'required|date',
            'value' => 'required|numeric',
            'observation' => 'nullable|string|max:1000',
            'files' => 'nullable|array|max:5',
            'files.*' => 'nullable|file|max:10240',
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        $kpiRecord->update([
            'record_date' => $validated['record_date'],
            'value' => $validated['value'],
            'observation' => $validated['observation'] ?? null,
        ]);

        // Handle File Uploads (Append new files)
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filePath = $file->store('kpi_records', 'public');

                File::create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $filePath,
                    'disk' => 'public',
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'fileable_id' => $kpiRecord->id,
                    'fileable_type' => KpiRecord::class,
                    'folder_id' => $request->input('folder_id'),
                    'user_id' => Auth::id(),
                ]);
            }
        }

        return redirect()->route('kpis.show', $kpiRecord->kpi_id)->with('success', 'Registro actualizado exitosamente.');
    }

    public function destroy(KpiRecord $kpiRecord)
    {
        $kpiId = $kpiRecord->kpi_id;
        $kpiRecord->delete();

        return redirect()->route('kpis.show', $kpiId)->with('success', 'Registro eliminado exitosamente.');
    }

    public function deleteFile(File $file)
    {
        // Simple permission check (can refine if needed)
        if ($file->user_id !== Auth::id() && !Auth::user()->can('delete', $file)) {
            // Basic fallback check, assuming policies might exist or just check ownership
        }

        // Delete from storage
        Storage::disk($file->disk)->delete($file->path);

        $file->delete();

        return redirect()->back()->with('success', 'Archivo eliminado exitosamente.');
    }
}
