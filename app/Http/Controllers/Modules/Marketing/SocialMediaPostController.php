<?php

namespace App\Http\Controllers\Modules\Marketing;

use App\Http\Controllers\Controller;
use App\Models\SocialMediaPost;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use App\Models\Project;
use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;

class SocialMediaPostController extends Controller
{
    public function index(Request $request)
    {
        $query = SocialMediaPost::with(['project', 'status', 'responsible']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('piece_name', 'like', '%' . $search . '%')
                    ->orWhere('comments', 'like', '%' . $search . '%');
            });
        }

        // Filters
        if ($request->filled('mediums_filter')) {
            $query->where('mediums', 'like', '%' . $request->mediums_filter . '%');
        }

        if ($request->filled('content_type_filter')) {
            $query->where('content_type', 'like', '%' . $request->content_type_filter . '%');
        }

        if ($request->filled('status_filter')) {
            $query->where('status_id', $request->status_filter);
        }

        if ($request->filled('project_filter')) {
            $query->where('project_id', $request->project_filter);
        }

        if ($request->filled('responsible_filter')) {
            $query->where('responsible_id', $request->responsible_filter);
        }

        if ($request->filled('date_from')) {
            $query->where('scheduled_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('scheduled_date', '<=', $request->date_to);
        }

        $posts = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('perPage', 10))
            ->withQueryString();

        // Options
        $statusCategory = TagCategory::where('slug', 'estado_publicacion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return Inertia::render('Modules/Marketing/SocialMedia/Index', [
            'posts' => $posts,
            'filters' => $request->only([
                'search',
                'mediums_filter',
                'content_type_filter',
                'status_filter',
                'project_filter',
                'responsible_filter',
                'date_from',
                'date_to',
                'perPage'
            ]),
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'users' => $users,
        ]);
    }

    public function create()
    {
        $statusCategory = TagCategory::where('slug', 'estado_publicacion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return Inertia::render('Modules/Marketing/SocialMedia/Form', [
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'users' => $users,
        ]);
    }

    public function store(Request $request, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $validated = $request->validate([
            'piece_name' => 'required|string|max:255',
            'mediums' => 'nullable|string|max:255',
            'content_type' => 'nullable|string|max:255',
            'scheduled_date' => 'nullable|date',
            'project_id' => 'nullable|exists:projects,id',
            'responsible_id' => 'nullable|exists:users,id',
            'status_id' => 'required|exists:tags,id',
            'status_id' => 'required|exists:tags,id',
            'comments' => 'nullable|string|max:1000',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $post = SocialMediaPost::create($validated);

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            // Obtener ID del área de marketing
            $areaId = Area::where('slug', 'marketing')->value('id');

            $folderId = null;
            if ($post->piece_name) {
                $folder = $folderMaker->execute($post->piece_name, null);
                $folderId = $folder->id;
            }

            $uploader->execute($files, $post, $folderId, $areaId);
        }

        if ($request->has('pending_file_ids')) {
            $areaId = Area::where('slug', 'marketing')->value('id');
            if ($areaId) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $post, $areaId);
                    }
                }
            }
        }

        return redirect()->route('marketing.socialmedia.index')->with('success', 'Publicación creada exitosamente.');
    }

    public function edit(SocialMediaPost $post) // Variable name must likely match route parameter, checking routes later.
    {
        $statusCategory = TagCategory::where('slug', 'estado_publicacion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        $post->load('files');

        return Inertia::render('Modules/Marketing/SocialMedia/Form', [
            'post' => $post,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'users' => $users,
        ]);
    }

    public function update(Request $request, SocialMediaPost $post, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $validated = $request->validate([
            'piece_name' => 'required|string|max:255',
            'mediums' => 'nullable|string|max:255',
            'content_type' => 'nullable|string|max:255',
            'scheduled_date' => 'nullable|date',
            'project_id' => 'nullable|exists:projects,id',
            'responsible_id' => 'nullable|exists:users,id',
            'status_id' => 'required|exists:tags,id',
            'comments' => 'nullable|string|max:1000',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $post->update($validated);

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            // Obtener ID del área de marketing
            $areaId = Area::where('slug', 'marketing')->value('id');

            $folderId = null;
            if ($post->piece_name) {
                $folder = $folderMaker->execute($post->piece_name, null);
                $folderId = $folder->id;
            }

            $uploader->execute($files, $post, $folderId, $areaId);
        }

        if ($request->has('pending_file_ids')) {
            $areaId = Area::where('slug', 'marketing')->value('id');
            if ($areaId) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $post, $areaId);
                    }
                }
            }
        }

        return redirect()->route('marketing.socialmedia.index')->with('success', 'Publicación actualizada exitosamente.');
    }

    public function destroy(SocialMediaPost $post)
    {
        $post->delete();
        return redirect()->route('marketing.socialmedia.index')->with('success', 'Publicación eliminada exitosamente.');
    }
}
