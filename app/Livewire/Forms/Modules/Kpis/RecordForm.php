<?php

namespace App\Livewire\Forms\Modules\Kpis;

use App\Models\Kpi;
use App\Models\KpiRecord;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use Livewire\WithFileUploads;

class RecordForm extends LivewireForm
{
    use WithFileUploads;
    
    public $kpi_id;
    public $record_date;
    public $value;
    public $observation;
    public $record_id = null;
    
    // Para archivos
    public $files = [];
    public $files_db = [];
    
    // Para el selector de carpetas
    public $selectedFolder = null;
    public $selectedFolderPath = null;
    public $selectedFolderId = null;

    protected $rules = [
        'kpi_id' => 'required|exists:kpis,id',
        'record_date' => 'required|date',
        'value' => 'required|numeric',
        'observation' => 'nullable|string|max:1000',
        'files' => 'nullable|array|max:5',
        'files.*' => 'nullable|file|max:10240', // 10MB max per file
    ];

    public function save()
    {
        $this->validate();
        
        // Guardar el ID del KPI antes de resetear
        $kpiId = $this->kpi_id;

        $record = KpiRecord::create([
            'kpi_id' => $this->kpi_id,
            'record_date' => $this->record_date,
            'value' => $this->value,
            'observation' => $this->observation,
            'created_by_id' => Auth::id(),
        ]);

        // Guardar archivos si existen
        if (!empty($this->files)) {
            foreach ($this->files as $file) {
                $filePath = $file->store('kpi_records', 'public');
                
                \App\Models\File::create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $filePath,
                    'disk' => 'public',
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'fileable_id' => $record->id,
                    'fileable_type' => KpiRecord::class,
                    'folder_id' => $this->selectedFolderId,
                    'user_id' => Auth::id(),
                ]);
            }
        }

        $this->reset();

        session()->flash('success', 'Registro creado exitosamente.');

        return redirect()->route('kpis.show', $kpiId);
    }

    public function update()
    {
        $this->validate();
        
        // Guardar el ID del KPI antes de resetear
        $record = KpiRecord::findOrFail($this->record_id);
        $kpiId = $record->kpi_id;
        
        $record->update([
            'record_date' => $this->record_date,
            'value' => $this->value,
            'observation' => $this->observation,
        ]);

        // Guardar nuevos archivos si existen
        if (!empty($this->files)) {
            foreach ($this->files as $file) {
                $filePath = $file->store('kpi_records', 'public');
                
                \App\Models\File::create([
                    'name' => $file->getClientOriginalName(),
                    'path' => $filePath,
                    'disk' => 'public',
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'fileable_id' => $record->id,
                    'fileable_type' => KpiRecord::class,
                    'folder_id' => $this->selectedFolderId,
                    'user_id' => Auth::id(),
                ]);
            }
        }

        $this->reset();

        session()->flash('success', 'Registro actualizado exitosamente.');

        return redirect()->route('kpis.show', $kpiId);
    }

    public function setKpi(Kpi $kpi)
    {
        $this->kpi_id = $kpi->id;
        $this->record_date = now()->format('Y-m-d');
    }

    public function setRecord(KpiRecord $record)
    {
        $this->record_id = $record->id;
        $this->kpi_id = $record->kpi_id;
        $this->record_date =\Carbon\Carbon::parse($record->record_date)->format('Y-m-d');
        $this->value = $record->value;
        $this->observation = $record->observation;
        
        // Cargar archivos existentes
        $this->files_db = $record->files;
    }
    
    // Métodos para el selector de carpetas
    public function handleFolderSelected($data)
    {
        $this->selectedFolder = $data["id"];
        $this->selectedFolderPath = $data["path"];
        $this->selectedFolderId = $data["id"];
        
        session()->flash('success', 'Carpeta seleccionada: ' . $data["name"]);
    }
    
    public function clearSelectedFolder()
    {
        $this->selectedFolder = null;
        $this->selectedFolderPath = null;
        $this->selectedFolderId = null;
    }
    
    public function removeTempFile($index)
    {
        unset($this->files[$index]);
        $this->files = array_values($this->files);
    }
    
    public function removeStoredFile($id)
    {
        $file = \App\Models\File::findOrFail($id);
        
        // Verificar permisos
        if (!$file->hasPermission(Auth::user(), 'delete')) {
            session()->flash('error', 'No tienes permiso para eliminar este archivo.');
            return;
        }
        
        $file->delete();
        
        session()->flash('success', 'Archivo eliminado exitosamente.');
    }
    
    public function cleanupTempFiles()
    {
        $this->cleanupOldUploads();
    }
}