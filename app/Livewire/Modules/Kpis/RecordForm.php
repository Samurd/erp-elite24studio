<?php

namespace App\Livewire\Modules\Kpis;

use App\Livewire\Forms\Modules\Kpis\RecordForm as KpiRecordForm;
use App\Models\Kpi;
use App\Models\KpiRecord;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class RecordForm extends Component
{
    use WithFileUploads;
    
    public KpiRecordForm $form;
    public $kpi;
    public $record;
    
    protected $listeners = [
        "open-folder-selector" => "openFolderSelector",
        "folder-selected" => "handleFolderSelected",
    ];
    
    // Para el selector de carpetas
    public $selectedFolder = null;
    public $selectedFolderPath = null;
    public $selectedFolderId = null;
    
    // Para archivos
    public $files = [];
    public $files_db = [];

    public function mount($kpi = null, $record = null)
    {
        if ($record) {
            $this->record = KpiRecord::findOrFail($record);
            $this->form->setRecord($this->record);
            $this->kpi = $this->record->kpi;
            
            // Cargar archivos existentes
            $this->files_db = $this->record->files;
        } elseif ($kpi) {
            $this->kpi = Kpi::findOrFail($kpi);
            $this->form->setKpi($this->kpi);
        }
    }

    public function save()
    {
        if ($this->record) {
            $this->form->update();
        } else {
            $this->form->save();
        }
    }

    public function render()
    {
        return view('livewire.modules.kpis.record-form', [
            'kpi' => $this->kpi,
            'record' => $this->record,
        ]);
    }
    
    public function openFolderSelector()
    {
        $this->dispatch("open-folder-selector");
    }
    
    public function clearSelectedFolder()
    {
        $this->selectedFolder = null;
        $this->selectedFolderPath = null;
        $this->selectedFolderId = null;
    }
    
    public function removeTempFile($index)
    {
        $this->removeFile($index);
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
    
    public function handleFolderSelected($data)
    {
        $this->selectedFolder = $data["id"];
        $this->selectedFolderPath = $data["path"];
        $this->selectedFolderId = $data["id"];
        
        session()->flash('success', 'Carpeta seleccionada: ' . $data["name"]);
    }
}