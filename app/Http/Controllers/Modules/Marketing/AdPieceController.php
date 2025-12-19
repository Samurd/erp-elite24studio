<?php

namespace App\Http\Controllers\Modules\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Adpiece;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Project;
use App\Models\Team;
use App\Models\Strategy;
use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;

class AdPieceController extends Controller
{
    public function index(Request $request)
    {
        $query = Adpiece::with(['type', 'format', 'status', 'project', 'responsible', 'strategy']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('media', 'like', '%' . $search . '%');
            });
        }

        // Filters
        if ($request->filled('type_filter')) {
            $query->where('type_id', $request->type_filter);
        }

        if ($request->filled('format_filter')) {
            $query->where('format_id', $request->format_filter);
        }

        if ($request->filled('status_filter')) {
            $query->where('status_id', $request->status_filter);
        }

        if ($request->filled('project_filter')) {
            $query->where('project_id', $request->project_filter);
        }

        if ($request->filled('team_filter')) {
            $query->where('team_id', $request->team_filter);
        }

        if ($request->filled('strategy_filter')) {
            $query->where('strategy_id', $request->strategy_filter);
        }

        if ($request->filled('media_filter')) {
            $query->where('media', 'like', '%' . $request->media_filter . '%');
        }

        $adpieces = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('perPage', 10))
            ->withQueryString();

        // Options
        $typeCategory = TagCategory::where('slug', 'tipo_pieza')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $formatCategory = TagCategory::where('slug', 'formato')->first();
        $formatOptions = $formatCategory ? Tag::where('category_id', $formatCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_pieza')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $projects = Project::orderBy('name')->get();
        // Assuming Team model usage is correct here, reusing what was in Livewire component usage
        $teams = Team::orderBy('name')->get();
        $strategies = Strategy::orderBy('name')->get();

        return Inertia::render('Modules/Marketing/AdPieces/Index', [
            'adpieces' => $adpieces,
            'filters' => $request->only([
                'search',
                'type_filter',
                'format_filter',
                'status_filter',
                'project_filter',
                'team_filter',
                'strategy_filter',
                'media_filter',
                'perPage'
            ]),
            'typeOptions' => $typeOptions,
            'formatOptions' => $formatOptions,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'teams' => $teams,
            'strategies' => $strategies,
        ]);
    }

    public function create()
    {
        $typeCategory = TagCategory::where('slug', 'tipo_pieza')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $formatCategory = TagCategory::where('slug', 'formato')->first();
        $formatOptions = $formatCategory ? Tag::where('category_id', $formatCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_pieza')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $projects = Project::orderBy('name')->get();
        $teams = Team::orderBy('name')->get();
        $strategies = Strategy::orderBy('name')->get();

        return Inertia::render('Modules/Marketing/AdPieces/Form', [
            'typeOptions' => $typeOptions,
            'formatOptions' => $formatOptions,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'teams' => $teams,
            'strategies' => $strategies,
        ]);
    }

    public function store(Request $request, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type_id' => 'nullable|exists:tags,id',
            'format_id' => 'nullable|exists:tags,id',
            'status_id' => 'nullable|exists:tags,id',
            'project_id' => 'nullable|exists:projects,id',
            'team_id' => 'nullable|exists:teams,id',
            'strategy_id' => 'nullable|exists:strategies,id',
            'media' => 'nullable|string|max:255',
            'instructions' => 'nullable|string',
        ]);

        $adpiece = Adpiece::create($validated);

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            // Obtener ID del área de marketing
            $areaId = Area::where('slug', 'marketing')->value('id');

            $folderId = null;
            if ($adpiece->name) {
                $folder = $folderMaker->execute($adpiece->name, null);
                $folderId = $folder->id;
            }

            $uploader->execute($files, $adpiece, $folderId, $areaId);
        }

        return redirect()->route('marketing.ad-pieces.index')->with('success', 'Pieza publicitaria creada exitosamente.');
    }

    public function edit(Adpiece $adpiece)
    {
        $typeCategory = TagCategory::where('slug', 'tipo_pieza')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $formatCategory = TagCategory::where('slug', 'formato')->first();
        $formatOptions = $formatCategory ? Tag::where('category_id', $formatCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_pieza')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $projects = Project::orderBy('name')->get();
        $teams = Team::orderBy('name')->get();
        $strategies = Strategy::orderBy('name')->get();

        $adpiece->load('files');

        return Inertia::render('Modules/Marketing/AdPieces/Form', [
            'adpiece' => $adpiece,
            'typeOptions' => $typeOptions,
            'formatOptions' => $formatOptions,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'teams' => $teams,
            'strategies' => $strategies,
        ]);
    }

    public function update(Request $request, Adpiece $adpiece, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type_id' => 'nullable|exists:tags,id',
            'format_id' => 'nullable|exists:tags,id',
            'status_id' => 'nullable|exists:tags,id',
            'project_id' => 'nullable|exists:projects,id',
            'team_id' => 'nullable|exists:teams,id',
            'strategy_id' => 'nullable|exists:strategies,id',
            'media' => 'nullable|string|max:255',
            'instructions' => 'nullable|string',
        ]);

        $adpiece->update($validated);

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            // Obtener ID del área de marketing
            $areaId = Area::where('slug', 'marketing')->value('id');

            $folderId = null;
            if ($adpiece->name) {
                $folder = $folderMaker->execute($adpiece->name, null);
                $folderId = $folder->id;
            }

            $uploader->execute($files, $adpiece, $folderId, $areaId);
        }

        return redirect()->route('marketing.ad-pieces.index')->with('success', 'Pieza publicitaria actualizada exitosamente.');
    }

    public function destroy(Adpiece $adpiece)
    {
        $adpiece->delete();
        return redirect()->route('marketing.ad-pieces.index')->with('success', 'Pieza publicitaria eliminada exitosamente.');
    }
}
