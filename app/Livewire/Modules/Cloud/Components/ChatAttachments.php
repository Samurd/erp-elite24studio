<?php

namespace App\Livewire\Modules\Cloud\Components;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\File;
use App\Models\Area;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Files\LinkFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;
use App\Models\Message;

class ChatAttachments extends Component
{
    use WithFileUploads;

    // Configuración
    public $areaSlug = 'teams'; // Default area for chat files

    // Estado de Subida (Temporales)
    public $uploads = []; // Array de TemporaryUploadedFile
    public $upload;       // Archivo individual en proceso

    // Estado de Vínculos Cloud (IDs seleccionados)
    public $pendingLinkIds = [];
    public $pendingLinkFiles = []; // Modelos File para mostrar info visual

    public $showModal = false;

    protected $listeners = [
        'file-selected-for-chat' => 'addCloudFile', // Respuesta del Modal Selector
        'clear-chat-attachments' => 'clearAttachments', // Limpiar después de enviar mensaje
        'commit-chat-attachments' => 'commit' // Procesar adjuntos
    ];

    /**
     * Hook que se ejecuta tras subir un archivo temporalmente.
     */
    public function updatedUpload()
    {
        $this->validate(['upload' => 'required|file|max:51200']); // 50MB

        // Agregar a la cola local
        $this->uploads[] = $this->upload;

        // Notificar al padre (Teams\Show) sobre el nuevo archivo temporal
        // Enviamos el objeto TemporaryUploadedFile para que el padre pueda procesarlo
        // Nota: Livewire no serializa bien los objetos TemporaryUploadedFile en eventos complejos,
        // así que mejor manejamos la subida aquí y solo notificamos al padre cuando se confirma el envío.
        // PERO, para simplificar, el padre delegará la subida a este componente o viceversa.
        // En este caso, vamos a emitir un evento con la info básica y mantener el estado aquí.
        // Cuando el padre quiera enviar el mensaje, pedirá a este componente que procese los archivos.

        // Mejor enfoque: El padre (Show.php) tendrá un listener para 'attachments-updated'
        // y este componente le enviará la lista actual de uploads y links.
        $this->emitAttachmentsUpdate();

        // Limpiar para el siguiente
        $this->upload = null;
        $this->dispatch('clear-file-input'); // Resetear input file en JS
    }

    /**
     * Quitar un archivo de la cola de subida.
     */
    public function deleteUpload(int $index)
    {
        array_splice($this->uploads, $index, 1);
        $this->emitAttachmentsUpdate();
    }

    /**
     * Abrir el modal para seleccionar archivos existentes.
     */
    public function openSelector()
    {
        // Obtener ID del área para pasarlo al modal (contexto)
        $areaId = Area::where('slug', $this->areaSlug)->value('id');

        $this->dispatch(
            'openFileSelector',
            type: 'App\Models\Message', // Tipo dummy o real, no importa mucho aquí porque id es null
            id: null,
            area_id: $areaId,
            eventName: 'file-selected-for-chat' // Evento de respuesta personalizado
        );
    }

    /**
     * Callback cuando se selecciona un archivo en el modal.
     */
    public function addCloudFile($fileId)
    {
        // Evitar duplicados visuales
        if (!in_array($fileId, $this->pendingLinkIds)) {
            $this->pendingLinkIds[] = $fileId;

            // Cargar el modelo para mostrar nombre/icono en la lista
            $file = File::find($fileId);
            if ($file) {
                $this->pendingLinkFiles[] = $file;
            }

            $this->emitAttachmentsUpdate();
        }

        $this->showModal = true; // Asegurar que el modal/popover esté visible
    }

    /**
     * Quitar un archivo del Cloud de la lista de pendientes.
     */
    public function removeCloudFile($index)
    {
        unset($this->pendingLinkIds[$index]);
        unset($this->pendingLinkFiles[$index]);

        // Reindexar
        $this->pendingLinkIds = array_values($this->pendingLinkIds);
        $this->pendingLinkFiles = array_values($this->pendingLinkFiles);

        $this->emitAttachmentsUpdate();
    }

    /**
     * Notificar al padre sobre los adjuntos actuales.
     */
    public function emitAttachmentsUpdate()
    {
        // Enviar solo datos seguros (arrays) para la UI del padre
        $uploadsData = collect($this->uploads)->map(function ($file) {
            return [
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'is_upload' => true
            ];
        })->toArray();

        $linksData = collect($this->pendingLinkFiles)->map(function ($file) {
            return [
                'id' => $file->id,
                'name' => $file->name,
                'size' => $file->size,
                'mime_type' => $file->mime_type,
                'url' => $file->url,
                'readable_size' => $file->readable_size,
                'is_upload' => false
            ];
        })->toArray();

        $this->dispatch('chat-attachments-updated', [
            'uploads' => $uploadsData,
            'links' => $linksData
        ]);
    }

    public function commit($messageId)
    {
        $message = Message::find($messageId);
        if (!$message)
            return;

        // 1. Procesar UPLOADS NUEVOS (Físicos)
        if (!empty($this->uploads)) {
            $uploader = app(UploadFileAction::class);
            $folderMaker = app(GetOrCreateFolderAction::class);

            // Carpeta por defecto
            $folder = $folderMaker->execute('Chat Files', null);
            $folderId = $folder->id;

            $areaId = Area::where('slug', $this->areaSlug)->value('id');

            $uploader->execute(
                $this->uploads,
                $message,
                $folderId,
                $areaId
            );
        }

        // 2. Procesar VÍNCULOS DE CLOUD (Existentes)
        if (!empty($this->pendingLinkIds)) {
            $linker = app(LinkFileAction::class);
            $areaId = Area::where('slug', $this->areaSlug)->value('id');

            foreach ($this->pendingLinkIds as $fileId) {
                $file = File::find($fileId);
                if ($file) {
                    $linker->execute($file, $message, $areaId);
                }
            }
        }

        // Limpiar después de procesar
        $this->clearAttachments();

        // Notificar al padre que ya se procesaron los adjuntos para que pueda hacer el broadcast
        $this->dispatch('chat-attachments-committed', messageId: $message->id);
    }

    /**
     * Limpiar adjuntos (llamado después de enviar mensaje)
     */
    public function clearAttachments()
    {
        $this->uploads = [];
        $this->pendingLinkIds = [];
        $this->pendingLinkFiles = [];
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.modules.cloud.components.chat-attachments');
    }
}
