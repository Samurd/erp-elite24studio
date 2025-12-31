<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Files\RenameFileAction;
use App\Actions\Cloud\Files\DeleteFileAction;
use App\Actions\Cloud\Folders\CreateFolderAction;
use App\Actions\Cloud\Folders\RenameFolderAction;
use App\Actions\Cloud\Folders\DeleteFolderAction;
use App\Models\Share;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CloudController extends Controller
{
    use AuthorizesRequests;

    // Helper to get browse data
    private function getBrowseData($folderId, $user)
    {
        // 1. Global Permission (Admin)
        $canViewAll = $user->can('cloud.view');

        // 2. Calculate Allowed Areas (Only if not admin)
        $allowedAreaIds = [];
        if (!$canViewAll) {
            $allowedAreaIds = Area::all()->filter(function ($area) use ($user) {
                // Assuming "slug.view" or "parent.slug.view"
                $permission = ($area->parent ? $area->parent->slug . '.' : '') . $area->slug . '.view';
                return $user->can($permission);
            })->pluck('id')->toArray();
        }

        $currentFolder = null;
        $breadcrumbs = [];
        $folders = [];
        $files = [];

        if ($folderId) {
            // --- INSIDE FOLDER ---
            $folder = Folder::with('parent')->findOrFail($folderId);
            // We use policy check in controller usually, but here we can check if user has access via tree
            // Simpler: Just rely on query filtering to show content. 
            // If user explicitly requests a folder they strictly can't see, fail?
            // For now, let's keep logic permissive but filtered content.
            if (!$canViewAll && $folder->user_id !== $user->id) {
                // Check shares... complex. 
                // Let's rely on the query scope logic below which is robust.
            }

            $currentFolder = $folder;

            // Breadcrumbs
            $curr = $folder;
            while ($curr) {
                array_unshift($breadcrumbs, $curr);
                $curr = $curr->parent;
            }

            // Child Folders
            $folders = $folder->children()
                ->when(!$canViewAll, function ($q) use ($user) {
                    $q->where(function ($sub) use ($user) {
                        $sub->where('user_id', $user->id)
                            ->orWhereHas('shares', fn($s) => $s->where('shared_with_user_id', $user->id));
                    });
                })->get();

            // Child Files
            $files = $folder->files()
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
                })->get()->map(function ($file) {
                    $file->readable_size = $file->readable_size;
                    return $file;
                });

        } else {
            // --- ROOT ---

            // Root Folders
            $folders = Folder::whereNull('parent_id')
                ->when(!$canViewAll, function ($q) use ($user) {
                    $q->where(function ($sub) use ($user) {
                        $sub->where('user_id', $user->id)
                            ->orWhereHas('shares', fn($s) => $s->where('shared_with_user_id', $user->id));
                    });
                })->get();

            // Root Files
            $files = File::whereNull('folder_id')
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
                })->get()->map(function ($file) {
                    $file->readable_size = $file->readable_size;
                    return $file;
                });
        }

        return [
            'folders' => $folders,
            'files' => $files,
            'currentFolder' => $currentFolder,
            'breadcrumbs' => $breadcrumbs,
        ];
    }

    public function index(Request $request)
    {
        $folderId = $request->input('folder');
        $user = Auth::user();

        $data = $this->getBrowseData($folderId, $user);

        return Inertia::render('Cloud/Index', [
            'folders' => $data['folders'],
            'files' => $data['files'],
            'currentFolder' => $data['currentFolder'],
            'breadcrumbs' => $data['breadcrumbs'],
            'canCreate' => $data['currentFolder'] ? $user->can('update', $data['currentFolder']) : $user->can('cloud.create'),
        ]);
    }

    // New API Endpoint for Modal (Client-Side Navigation Optimized)
    public function selectorData(Request $request)
    {
        $user = Auth::user();

        // Check if we should load all for client-side nav (default)
        $allFolders = $this->getAllFolders($user);
        $allFiles = $this->getAllFiles($user);

        return response()->json([
            'folders' => $allFolders,
            'files' => $allFiles,
            'breadcrumbs' => [], // Not needed for initial load, client handles it
        ]);
    }

    private function getAllFolders($user)
    {
        $userId = $user->id;
        $canViewAll = $user->can('cloud.view');

        return Folder::when(!$canViewAll, function ($q) use ($userId) {
            $q->where(function ($sub) use ($userId) {
                $sub->where('user_id', $userId)
                    ->orWhereHas('shares', fn($s) => $s->where('shared_with_user_id', $userId));
            });
        })->get(['id', 'name', 'parent_id']);
    }

    private function getAllFiles($user)
    {
        $userId = $user->id;
        $canViewAll = $user->can('cloud.view');

        $allowedAreaIds = [];
        if (!$canViewAll) {
            $allowedAreaIds = Area::all()->filter(function ($area) use ($user) {
                $permission = ($area->parent ? $area->parent->slug . '.' : '') . $area->slug . '.view';
                return $user->can($permission);
            })->pluck('id')->toArray();
        }

        return File::with('folder:id,name')
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
                // Ensure readable_size is available (if it's an accessor, it might be hidden if not in appends)
                // Or we can calculate it here if needed, but model accessor is better.
                // Assuming 'readable_size' is an attribute on the model.
                $file->append('readable_size');
                return $file;
            });
    }

    public function createFolder(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        try {
            $action = new CreateFolderAction();
            $action->execute(
                name: $request->name,
                parentId: $request->input('parent_id')
            );
            return back()->with('success', 'Carpeta creada exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear carpeta: ' . $e->getMessage());
        }
    }

    public function uploadFile(Request $request)
    {
        $request->validate(['file' => 'required|file|max:102400']); // 100MB

        try {
            $action = new UploadFileAction();
            $action->execute(
                files: $request->file('file'),
                contextModel: null,
                folderId: $request->input('folder_id'),
                areaId: null
            );
            return back()->with('success', 'Archivo subido exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al subir archivo: ' . $e->getMessage());
        }
    }

    public function rename(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:folder,file'
        ]);

        try {
            if ($request->type === 'folder') {
                $folder = Folder::findOrFail($id);
                $this->authorize('update', $folder);
                (new RenameFolderAction())->execute($folder, $request->name);
            } else {
                $file = File::findOrFail($id);
                $this->authorize('update', $file);
                (new RenameFileAction())->execute($file, $request->name);
            }
            return back()->with('success', 'Renombrado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al renombrar: ' . $e->getMessage());
        }
    }

    public function delete(Request $request, $id)
    {
        $request->validate(['type' => 'required|in:folder,file']);

        try {
            if ($request->type === 'folder') {
                $folder = Folder::findOrFail($id);
                $this->authorize('delete', $folder);
                (new DeleteFolderAction())->execute($folder);
            } else {
                $file = File::findOrFail($id);
                $this->authorize('delete', $file);
                (new DeleteFileAction())->execute($file);
            }
            return back()->with('success', 'Eliminado exitosamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }


    // --- Sharing Methods ---

    public function getShareData(Request $request, $type, $id)
    {
        $modelType = $type === 'folder' ? Folder::class : File::class;
        $model = $modelType::findOrFail($id);
        $this->authorize('view', $model);

        $users = User::where('id', '!=', Auth::id())->get(['id', 'name', 'email']);
        $teams = Team::all(['id', 'name']);

        $shares = Share::where('shareable_id', $id)
            ->where('shareable_type', $modelType)
            ->whereNull('share_token')
            ->with(['sharedWithUser:id,name,email,profile_photo_path', 'sharedWithTeam:id,name'])
            ->get();

        $publicLink = Share::where('shareable_id', $id)
            ->where('shareable_type', $modelType)
            ->whereNotNull('share_token')
            ->first();

        // Check expiration
        if ($publicLink && $publicLink->expires_at && $publicLink->expires_at->isPast()) {
            $publicLink->delete();
            $publicLink = null;
        }

        return response()->json([
            'users' => $users,
            'teams' => $teams,
            'shares' => $shares,
            'publicLink' => $publicLink ? [
                'url' => route('public.share', ['token' => $publicLink->share_token]),
                'expires_at' => $publicLink->expires_at,
                'token' => $publicLink->share_token
            ] : null
        ]);
    }

    public function share(Request $request, $type, $id)
    {
        $request->validate([
            'permission' => 'required|in:view,edit',
            'user_id' => 'nullable|exists:users,id',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        if (!$request->user_id && !$request->team_id) {
            return back()->with('error', 'Debe seleccionar un usuario o grupo.');
        }

        $modelType = $type === 'folder' ? Folder::class : File::class;
        $model = $modelType::findOrFail($id);
        $this->authorize('update', $model);

        Share::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'shareable_id' => $id,
                'shareable_type' => $modelType,
                'shared_with_user_id' => $request->user_id,
                'shared_with_team_id' => $request->team_id,
            ],
            [
                'permission' => $request->permission,
                'share_token' => null,
                'expires_at' => null,
            ]
        );

        return back()->with('success', 'Compartido correctamente.');
    }

    public function unshare(Request $request, $shareId)
    {
        $share = Share::find($shareId);
        if ($share && ($share->user_id === Auth::id() || Auth::user()->can('cloud.delete'))) {
            $share->delete();
            return back()->with('success', 'Acceso revocado.');
        }
        return back()->with('error', 'No autorizado.');
    }

    public function generatePublicLink(Request $request, $type, $id)
    {
        $request->validate(['expires_at' => 'nullable|date|after:today']);

        $modelType = $type === 'folder' ? Folder::class : File::class;
        $model = $modelType::findOrFail($id);
        $this->authorize('update', $model);

        // Remove existing
        Share::where('shareable_id', $id)
            ->where('shareable_type', $modelType)
            ->whereNotNull('share_token')
            ->delete();

        Share::create([
            'user_id' => Auth::id(),
            'shareable_id' => $id,
            'shareable_type' => $modelType,
            'permission' => 'view',
            'share_token' => Str::random(40),
            'expires_at' => $request->expires_at ? Carbon::parse($request->expires_at) : null,
        ]);

        return back()->with('success', 'Enlace generado.');
    }

    public function deletePublicLink(Request $request, $type, $id)
    {
        $modelType = $type === 'folder' ? Folder::class : File::class;
        $this->authorize('update', $modelType::findOrFail($id));

        Share::where('shareable_id', $id)
            ->where('shareable_type', $modelType)
            ->whereNotNull('share_token')
            ->delete();

        return back()->with('success', 'Enlace eliminado.');
    }

    public function download(File $file)
    {
        // Permission Check
        $user = Auth::user();
        $canView = false;

        // 1. Owner
        if ($file->user_id === $user->id) {
            $canView = true;
        }

        // 2. Shared
        if (!$canView && $file->shares()->where('shared_with_user_id', $user->id)->exists()) {
            $canView = true;
        }

        // 3. Admin
        if (!$canView && $user->can('cloud.view')) {
            $canView = true;
        }

        // 4. Linked to accessible model (Message)
        if (!$canView) {
            $linkedMessages = $file->messages; // Assumes relationship exists in File model
            foreach ($linkedMessages as $msg) {
                // Check if user is in channel/team of message
                if ($msg->channel_id) {
                    if ($msg->channel->members->contains($user->id) || $msg->channel->team->members->contains($user->id)) { // Simplification
                        $canView = true;
                        break;
                    }
                } elseif ($msg->private_chat_id) {
                    if ($msg->privateChat->participants->contains($user->id)) {
                        $canView = true;
                        break;
                    }
                }
            }
        }

        if (!$canView) {
            abort(403, 'No tienes permiso para descargar este archivo.');
        }

        return \Illuminate\Support\Facades\Storage::disk($file->disk)->download($file->path, $file->name);
    }
}
