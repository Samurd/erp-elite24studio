<?php

namespace App\Livewire\Components;

use App\Models\File;
use App\Models\Folder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class FolderSelectorModal extends Component
{
    public $showModal = false;
    public $currentFolder = null;
    public $breadcrumbs = [];
    public $folders = [];

    // Modo de selección: 'folder', 'file', 'both'
    public $selectionMode = 'folder';

    // Para permitir selección de archivos si se necesita
    public $allowFileSelection = false;
    public $files = [];
    public $selectedPath = null;
    public $selectedType = null; // 'folder' o 'file'
    public $selectedId = null;

    // Filtros opcionales
    public $fileTypeFilter = null; // Ej: 'image/*', 'application/pdf'

    protected $listeners = [
        "open-folder-selector" => "openModal",
        "close-folder-selector" => "closeModal",
    ];

    public function openModal($options = [])
    {
        // Configurar modo de selección
        $this->selectionMode = $options['mode'] ?? 'folder';
        $this->allowFileSelection = $options["allow_files"] ?? ($this->selectionMode !== 'folder');
        $this->fileTypeFilter = $options['file_type_filter'] ?? null;

        $this->showModal = true;
        $this->loadFolder($options['initial_folder_id'] ?? null);
    }

    public function openModalForFile($options = [])
    {
        $this->allowFileSelection = false;
        $this->showModal = true;
        $this->loadFolder(null);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset([
            "currentFolder",
            "breadcrumbs",
            "folders",
            "files",
            "selectedPath",
            "selectedType",
            "selectedId",
        ]);
    }

    public function updatedShowModal($value)
    {
        if (!$value) {
            $this->closeModal();
        }
    }

    public function loadFolder($folderId = null)
    {
        $user = Auth::user();

        if ($folderId) {
            $folder = Folder::findOrFail($folderId);

            // Verificación de permisos heredados
            if (!$folder->hasPermission($user, "view")) {
                abort(403, "No tienes permiso para ver esta carpeta.");
            }

            $this->currentFolder = $folder;
            $this->breadcrumbs = $this->buildBreadcrumbs($folder);

            $this->folders = $folder
                ->children()
                ->get()
                ->filter(fn($f) => $f->hasPermission($user, "view"));

            if ($this->allowFileSelection) {
                $this->files = $folder
                    ->files()
                    ->get()
                    ->filter(fn($f) => $f->hasPermission($user, "view"));
            } else {
                $this->files = collect();
            }
        } else {
            $this->currentFolder = null;
            $this->breadcrumbs = [];

            $this->folders = Folder::whereNull("parent_id")
                ->get()
                ->filter(fn($f) => $f->hasPermission($user, "view"));

            if ($this->allowFileSelection) {
                $this->files = File::whereNull("folder_id")
                    ->get()
                    ->filter(fn($f) => $f->hasPermission($user, "view"));
            } else {
                $this->files = collect();
            }
        }
    }

    public function buildBreadcrumbs($folder)
    {
        $breadcrumbs = [];
        $current = $folder;
        while ($current) {
            array_unshift($breadcrumbs, $current);
            $current = $current->parent;
        }
        return $breadcrumbs;
    }

    public function goBack()
    {
        if ($this->currentFolder && $this->currentFolder->parent_id) {
            $this->openFolder($this->currentFolder->parent_id);
        } else {
            $this->openFolder(null);
        }
    }

    public function openFolder($folderId = null)
    {
        $this->loadFolder($folderId);
    }

    public function selectFolder($folderId)
    {
        $folder = Folder::findOrFail($folderId);
        $user = Auth::user();

        if (!$folder->hasPermission($user, "view")) {
            abort(403, "No tienes permiso para seleccionar esta carpeta.");
        }

        $this->selectedPath = $folder->path;
        $this->selectedType = "folder";
        $this->selectedId = $folder->id;

        $this->dispatch("folder-selected", [
            "path" => $folder->path,
            "type" => "folder",
            "id" => $folder->id,
            "name" => $folder->name,
        ]);

        $this->closeModal();
    }

    public function selectFile($fileId)
    {
        if (!$this->allowFileSelection) {
            return;
        }

        $file = \App\Models\File::findOrFail($fileId);
        $user = Auth::user();

        if (!$file->hasPermission($user, "view")) {
            abort(403, "No tienes permiso para seleccionar este archivo.");
        }

        $this->selectedPath = $file->path;
        $this->selectedType = "file";
        $this->selectedId = $file->id;

        $this->dispatch("file-selected", [
            "path" => $file->path,
            "type" => "file",
            "id" => $file->id,
            "name" => $file->name,
        ]);

        $this->closeModal();
    }

    public function render()
    {
        return view("livewire.components.folder-selector-modal", [
            "breadcrumbs" => $this->breadcrumbs,
            "folders" => $this->folders,
            "files" => $this->files,
            "currentFolder" => $this->currentFolder,
        ]);
    }
}
