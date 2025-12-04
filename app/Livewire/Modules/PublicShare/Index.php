<?php

namespace App\Livewire\Modules\PublicShare;

use App\Models\File;
use App\Models\Folder;
use App\Models\Share;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class Index extends Component
{
    public $token;
    public $share;
    public $currentFolder = null;
    public $breadcrumbs = [];
    public $folders = [];
    public $files = [];
    public $isFileShare = false;

    public function mount($token, $folder = null)
    {
        $this->token = $token;
        $this->share = Share::where("share_token", $token)->firstOrFail();

        // Verificar expiración
        if (
            $this->share->expires_at &&
            Carbon::parse($this->share->expires_at)->isPast()
        ) {
            abort(403, "Este enlace ha expirado.");
        }

        // Si el enlace apunta a un archivo
        if ($this->share->shareable instanceof File) {
            $this->isFileShare = true;
            $this->files = collect([$this->share->shareable]);
            return;
        }

        // Si el enlace apunta a una carpeta
        $this->currentFolder = $folder
            ? Folder::findOrFail($folder)
            : $this->share->shareable;

        $this->loadItems();
        $this->generateBreadcrumbs();
    }

    public function loadItems()
    {
        if ($this->currentFolder instanceof Folder) {
            $this->folders = $this->currentFolder->children()->get();
            $this->files = $this->currentFolder->files()->get();
        } else {
            $this->folders = collect();
            $this->files = collect();
        }
    }

    public function openFolder($folderId)
    {
        $this->currentFolder = Folder::findOrFail($folderId);
        $this->loadItems();
        $this->generateBreadcrumbs();
    }

    public function goBack()
    {
        if ($this->currentFolder && $this->currentFolder->parent_id) {
            $this->currentFolder = $this->currentFolder->parent;
            $this->loadItems();
            $this->generateBreadcrumbs();
        } else {
            $this->currentFolder = $this->share->shareable;
            $this->loadItems();
            $this->generateBreadcrumbs();
        }
    }

    public function downloadFile($fileId)
    {
        $file = File::findOrFail($fileId);
        return Storage::download($file->path, $file->name);
    }

    protected function generateBreadcrumbs()
    {
        $this->breadcrumbs = [];
        $folder = $this->currentFolder;

        while ($folder) {
            array_unshift($this->breadcrumbs, $folder);
            $folder = $folder->parent;
        }
    }

    public function render()
    {
        $layout = Auth::check() ? "layouts.app" : "layouts.guest";

        return view("livewire.modules.public-share.index")->layout($layout);
    }
}
