<?php

namespace App\Livewire\Modules\Cloud;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

// Modelos
use App\Models\File;
use App\Models\Folder;
use App\Models\Area;

// Actions
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Files\RenameFileAction;
use App\Actions\Cloud\Files\DeleteFileAction;
use App\Actions\Cloud\Folders\CreateFolderAction;
use App\Actions\Cloud\Folders\RenameFolderAction;
use App\Actions\Cloud\Folders\DeleteFolderAction;

class Index extends Component
{
    use WithFileUploads;
    use AuthorizesRequests;

    public $currentFolder = null;
    public $breadcrumbs = [];
    public $folders = [];
    public $files = [];

    // Upload
    public $uploadingFile;

    // Crear Carpeta
    public $newFolderName = "";

    // Renombrado
    public $renamingId = null;
    public $renamingType = null;
    public $newName = "";

    protected $listeners = ["folderChanged" => "loadFolder"];

    public function mount($folderId = null)
    {
        $this->loadFolder($folderId);
    }

    public function loadFolder($folderId = null)
    {
        $user = Auth::user();

        // 1. Permiso Global (Admin)
        $canViewAll = $user->can('cloud.view');

        // 2. Calcular Áreas permitidas (Solo si no es admin)
        // Esto permite ver archivos vinculados a áreas donde tengo permiso (ej: projects.view)
        $allowedAreaIds = [];
        if (!$canViewAll) {
            // Optimización: Traemos todas las áreas y filtramos en PHP para no hacer N queries
            // Asume permiso "slug.view" o "padre.slug.view"
            $allowedAreaIds = Area::all()->filter(function ($area) use ($user) {
                // Construir nombre del permiso: "projects.view"
                // O usar tu AreaPermissionService si prefieres
                $permission = ($area->parent ? $area->parent->slug . '.' : '') . $area->slug . '.view';
                return $user->can($permission);
            })->pluck('id')->toArray();
        }

        if ($folderId) {
            // --- DENTRO DE UNA CARPETA ---
            $folder = Folder::with('parent')->findOrFail($folderId);
            $this->authorize('view', $folder);

            $this->currentFolder = $folder;

            // Breadcrumbs (Array Objetos)
            $crumbs = [];
            $curr = $folder;
            while ($curr) {
                array_unshift($crumbs, $curr);
                $curr = $curr->parent;
            }
            $this->breadcrumbs = $crumbs;

            // Carpetas Hijas
            $this->folders = $folder->children()
                ->when(!$canViewAll, function ($q) use ($user) {
                    $q->where(function ($sub) use ($user) {
                        $sub->where('user_id', $user->id)
                            ->orWhereHas('shares', fn($s) => $s->where('shared_with_user_id', $user->id));
                    });
                })->get();

            // Archivos Hijos
            $this->files = $folder->files()
                ->when(!$canViewAll, function ($q) use ($user, $allowedAreaIds) {
                    $q->where(function ($sub) use ($user, $allowedAreaIds) {
                        $sub->where('user_id', $user->id)
                            ->orWhereHas('shares', fn($s) => $s->where('shared_with_user_id', $user->id))
                            // Lógica de Áreas: Si el archivo está vinculado a un área permitida
                            ->orWhereExists(function ($exists) use ($allowedAreaIds) {
                                $exists->select('id')
                                    ->from('files_links')
                                    ->whereColumn('files_links.file_id', 'files.id')
                                    ->whereIn('files_links.area_id', $allowedAreaIds);
                            });
                    });
                })->get();

        } else {
            // --- ROOT ---
            $this->currentFolder = null;
            $this->breadcrumbs = [];

            // Carpetas Root
            $this->folders = Folder::whereNull('parent_id')
                ->when(!$canViewAll, function ($q) use ($user) {
                    $q->where(function ($sub) use ($user) {
                        $sub->where('user_id', $user->id)
                            ->orWhereHas('shares', fn($s) => $s->where('shared_with_user_id', $user->id));
                    });
                })->get();

            // Archivos Root
            // Aquí es donde brillará la lógica de áreas: verás los archivos de tus proyectos en la raíz
            $this->files = File::whereNull('folder_id')
                ->when(!$canViewAll, function ($q) use ($user, $allowedAreaIds) {
                    $q->where(function ($sub) use ($user, $allowedAreaIds) {
                        $sub->where('user_id', $user->id)
                            ->orWhereHas('shares', fn($s) => $s->where('shared_with_user_id', $user->id))
                            ->orWhereExists(function ($exists) use ($allowedAreaIds) {
                                $exists->select('id')
                                    ->from('files_links')
                                    ->whereColumn('files_links.file_id', 'files.id')
                                    ->whereIn('files_links.area_id', $allowedAreaIds);
                            });
                    });
                })->get();
        }
    }

    public function openFolder($folderId = null)
    {
        $this->loadFolder($folderId);
    }

    public function createFolder()
    {
        $this->validate([
            'newFolderName' => 'required|string|max:255',
        ]);

        try {
            $action = new CreateFolderAction();
            $action->execute(
                name: $this->newFolderName,
                parentId: $this->currentFolder?->id
            );

            $this->newFolderName = '';
            $this->loadFolder($this->currentFolder?->id);
            $this->dispatch('notify', ['message' => 'Carpeta creada exitosamente', 'type' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'Error al crear carpeta: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function updatedUploadingFile()
    {
        $this->validate([
            'uploadingFile' => 'required|file|max:102400', // 100MB
        ]);

        try {
            $action = new UploadFileAction();
            $action->execute(
                files: $this->uploadingFile,
                contextModel: null,
                folderId: $this->currentFolder?->id,
                areaId: null
            );

            $this->uploadingFile = null;
            $this->loadFolder($this->currentFolder?->id);
            $this->dispatch('notify', ['message' => 'Archivo subido exitosamente', 'type' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'Error al subir archivo: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function startRename($id, $type)
    {
        $this->renamingId = $id;
        $this->renamingType = $type;

        if ($type === 'folder') {
            $folder = Folder::find($id);
            $this->authorize('update', $folder);
            $this->newName = $folder->name;
        } else {
            $file = File::find($id);
            $this->authorize('update', $file);
            $this->newName = $file->name;
        }
    }

    public function updateName()
    {
        $this->validate([
            'newName' => 'required|string|max:255',
        ]);

        try {
            if ($this->renamingType === 'folder') {
                $folder = Folder::findOrFail($this->renamingId);
                $this->authorize('update', $folder);

                $action = new RenameFolderAction();
                $action->execute($folder, $this->newName);
            } else {
                $file = File::findOrFail($this->renamingId);
                $this->authorize('update', $file);

                $action = new RenameFileAction();
                $action->execute($file, $this->newName);
            }

            $this->cancelRename();
            $this->loadFolder($this->currentFolder?->id);
            $this->dispatch('notify', ['message' => 'Renombrado exitosamente', 'type' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'Error al renombrar: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function cancelRename()
    {
        $this->renamingId = null;
        $this->renamingType = null;
        $this->newName = '';
    }

    public function deleteFolder($folderId)
    {
        try {
            $folder = Folder::findOrFail($folderId);
            $this->authorize('delete', $folder);

            $action = new DeleteFolderAction();
            $action->execute($folder);

            $this->loadFolder($this->currentFolder?->id);
            $this->dispatch('notify', ['message' => 'Carpeta eliminada exitosamente', 'type' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'Error al eliminar carpeta: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function deleteFile($fileId)
    {
        try {
            $file = File::findOrFail($fileId);
            $this->authorize('delete', $file);

            $action = new DeleteFileAction();
            $action->execute($file);

            $this->loadFolder($this->currentFolder?->id);
            $this->dispatch('notify', ['message' => 'Archivo eliminado exitosamente', 'type' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'Error al eliminar archivo: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function render()
    {
        return view("livewire.modules.cloud.index", [
            "breadcrumbs" => $this->breadcrumbs,
            "folders" => $this->folders,
            "files" => $this->files,
            "currentFolder" => $this->currentFolder,
        ]);
    }
}