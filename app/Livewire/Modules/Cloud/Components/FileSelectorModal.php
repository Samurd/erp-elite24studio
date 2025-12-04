<?php

namespace App\Livewire\Modules\Cloud\Components;

use Livewire\Component;
use App\Models\Folder;
use App\Models\File;
use App\Models\Area;
use App\Actions\Cloud\Files\LinkFileAction;
use Illuminate\Support\Facades\Auth;

class FileSelectorModal extends Component
{
    // Contexto de vinculación
    public $contextType;
    public $contextId;      // Puede ser null (Modo Creación)
    public $areaId = null;  // ID del área para permisos (ej: 5)

    // Navegación
    public $currentFolderId = null;
    public $breadcrumbs = [];

    // Selección
    public $selectedFileId = null;

    protected $listeners = ['openFileSelector'];

    /**
     * Configura el contexto del modal (llamado desde Alpine.js).
     */
    public function setContext($type, $id, $area_id = null)
    {
        $this->contextType = $type;
        $this->contextId = $id;
        $this->areaId = $area_id;

        // Resetear estado
        $this->currentFolderId = null;
        $this->selectedFileId = null;
        $this->updateBreadcrumbs();
    }

    /**
     * Método legacy para compatibilidad (ya no se usa).
     */
    public function openFileSelector($type, $id, $area_id = null)
    {
        $this->setContext($type, $id, $area_id);
    }

    /**
     * Carga datos de carpeta para Alpine.js (navegación instantánea).
     */
    public function loadFolderData($folderId = null)
    {
        $this->currentFolderId = $folderId;
        $this->updateBreadcrumbs();

        return [
            'folders' => $this->getFolders()->toArray(),
            'files' => $this->getFiles()->toArray(),
            'breadcrumbs' => $this->breadcrumbs
        ];
    }

    /**
     * Método legacy - ya no se usa (Alpine.js maneja selección).
     */
    public function selectFile($fileId)
    {
        $this->selectedFileId = ($this->selectedFileId === $fileId) ? null : $fileId;
    }

    public function confirmSelection($fileId = null)
    {
        \Illuminate\Support\Facades\Log::info('FileSelectorModal: confirmSelection called', [
            'fileId_param' => $fileId,
            'selectedFileId_prop' => $this->selectedFileId,
            'contextType' => $this->contextType,
            'contextId' => $this->contextId,
            'areaId' => $this->areaId
        ]);

        $linker = app(LinkFileAction::class);

        // Usar el parámetro si se pasa, sino usar la propiedad
        $selectedId = $fileId ?? $this->selectedFileId;

        if (!$selectedId) {
            \Illuminate\Support\Facades\Log::warning('FileSelectorModal: No selectedId');
            return;
        }

        $file = File::find($selectedId);
        if (!$file) {
            \Illuminate\Support\Facades\Log::warning('FileSelectorModal: File not found', ['id' => $selectedId]);
            return;
        }

        // CASO B: MODO CREACIÓN (ID es null) -> Emitir evento al padre
        if (!$this->contextId) {
            \Illuminate\Support\Facades\Log::info('FileSelectorModal: Creation Mode');
            $this->dispatch('file-selected-for-creation', $file->id);
            $this->dispatch('notify', 'Archivo seleccionado para vincular');
        }
        // CASO A: MODO EDICIÓN (Tenemos ID) -> Vincular directo en BD
        else {
            \Illuminate\Support\Facades\Log::info('FileSelectorModal: Edit Mode');
            $modelClass = $this->contextType;
            if (class_exists($modelClass)) {
                $model = $modelClass::find($this->contextId);

                if ($model) {
                    \Illuminate\Support\Facades\Log::info('FileSelectorModal: Linking file', ['model' => $model->id, 'file' => $file->id]);
                    try {
                        $linker->execute($file, $model, $this->areaId);
                        \Illuminate\Support\Facades\Log::info('FileSelectorModal: Linked successfully');

                        $this->dispatch('file-linked');
                        $this->dispatch('notify', 'Archivo vinculado correctamente');
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('FileSelectorModal: Error linking', ['error' => $e->getMessage()]);
                    }
                } else {
                    \Illuminate\Support\Facades\Log::warning('FileSelectorModal: Model not found', ['class' => $modelClass, 'id' => $this->contextId]);
                }
            } else {
                \Illuminate\Support\Facades\Log::warning('FileSelectorModal: Class not exists', ['class' => $modelClass]);
            }
        }

        // Cerrar modal via Alpine.js
        $this->dispatch('close-file-selector-modal');
    }

    private function updateBreadcrumbs()
    {
        $crumbs = [];
        if ($this->currentFolderId) {
            $folder = Folder::with('parent')->find($this->currentFolderId);
            $curr = $folder;
            while ($curr) {
                array_unshift($crumbs, ['id' => $curr->id, 'name' => $curr->name]);
                $curr = $curr->parent;
            }
        }
        $this->breadcrumbs = $crumbs;
    }

    private function getFolders()
    {
        $user = Auth::user();
        $userId = $user->id;
        $canViewAll = $user->can('cloud.view');

        return Folder::where('parent_id', $this->currentFolderId)
            ->when(!$canViewAll, function ($q) use ($userId) {
                $q->where(function ($sub) use ($userId) {
                    $sub->where('user_id', $userId)
                        ->orWhereHas('shares', fn($s) => $s->where('shared_with_user_id', $userId));
                });
            })->get();
    }

    private function getFiles()
    {
        $user = Auth::user();
        $userId = $user->id;
        $canViewAll = $user->can('cloud.view');

        $allowedAreaIds = [];
        if (!$canViewAll) {
            $allowedAreaIds = Area::all()->filter(function ($area) use ($user) {
                $perm = ($area->parent ? $area->parent->slug . '.' : '') . $area->slug . '.view';
                return $user->can($perm);
            })->pluck('id')->toArray();
        }

        $files = File::with('folder');

        if ($this->currentFolderId) {
            $files->where('folder_id', $this->currentFolderId)
                ->when(!$canViewAll, function ($q) use ($userId, $allowedAreaIds) {
                    $q->where(function ($sub) use ($userId, $allowedAreaIds) {
                        $sub->where('user_id', $userId)
                            ->orWhereHas('shares', fn($s) => $s->where('shared_with_user_id', $userId))
                            ->orWhereExists(function ($exists) use ($allowedAreaIds) {
                                $exists->select('id')
                                    ->from('files_links')
                                    ->whereColumn('files_links.file_id', 'files.id')
                                    ->whereIn('files_links.area_id', $allowedAreaIds);
                            });
                    });
                });
        } else {
            $files->where(function ($query) use ($userId, $allowedAreaIds, $canViewAll) {
                $query->where(function ($q) use ($userId, $canViewAll) {
                    $q->where('folder_id', null);
                    if (!$canViewAll) {
                        $q->where(function ($perm) use ($userId) {
                            $perm->where('user_id', $userId)
                                ->orWhereHas('shares', fn($s) => $s->where('shared_with_user_id', $userId));
                        });
                    }
                });
                if (!empty($allowedAreaIds)) {
                    $query->orWhereExists(function ($exists) use ($allowedAreaIds) {
                        $exists->select('id')
                            ->from('files_links')
                            ->whereColumn('files_links.file_id', 'files.id')
                            ->whereIn('files_links.area_id', $allowedAreaIds);
                    });
                }
            });
        }

        return $files->get();
    }

    public function render()
    {
        // Cargar TODAS las carpetas y archivos de una vez para navegación instantánea
        $allFolders = $this->getAllFolders();
        $allFiles = $this->getAllFiles();

        return view('livewire.modules.cloud.components.file-selector-modal', [
            'allFolders' => $allFolders,
            'allFiles' => $allFiles,
            'breadcrumbs' => $this->breadcrumbs
        ]);
    }

    private function getAllFolders()
    {
        $user = Auth::user();
        $userId = $user->id;
        $canViewAll = $user->can('cloud.view');

        return Folder::when(!$canViewAll, function ($q) use ($userId) {
            $q->where(function ($sub) use ($userId) {
                $sub->where('user_id', $userId)
                    ->orWhereHas('shares', fn($s) => $s->where('shared_with_user_id', $userId));
            });
        })->get(['id', 'name', 'parent_id'])->toArray();
    }

    private function getAllFiles()
    {
        $user = Auth::user();
        $userId = $user->id;
        $canViewAll = $user->can('cloud.view');

        $allowedAreaIds = [];
        if (!$canViewAll) {
            $allowedAreaIds = Area::all()->filter(function ($area) use ($user) {
                $perm = ($area->parent ? $area->parent->slug . '.' : '') . $area->slug . '.view';
                return $user->can($perm);
            })->pluck('id')->toArray();
        }

        $files = File::with('folder:id,name')
            ->when(!$canViewAll, function ($q) use ($userId, $allowedAreaIds) {
                $q->where(function ($sub) use ($userId, $allowedAreaIds) {
                    $sub->where('user_id', $userId)
                        ->orWhereHas('shares', fn($s) => $s->where('shared_with_user_id', $userId));

                    if (!empty($allowedAreaIds)) {
                        $sub->orWhereExists(function ($exists) use ($allowedAreaIds) {
                            $exists->select('id')
                                ->from('files_links')
                                ->whereColumn('files_links.file_id', 'files.id')
                                ->whereIn('files_links.area_id', $allowedAreaIds);
                        });
                    }
                });
            })
            ->get(['id', 'name', 'folder_id', 'mime_type', 'path', 'disk', 'size'])
            ->map(function ($file) {
                // Agregar propiedades generadas dinámicamente via accessors
                return [
                    'id' => $file->id,
                    'name' => $file->name,
                    'folder_id' => $file->folder_id,
                    'mime_type' => $file->mime_type,
                    'url' => $file->url,
                    'readable_size' => $file->readable_size,
                    'folder' => $file->folder
                ];
            })
            ->toArray();

        return $files;
    }
}