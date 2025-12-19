<?php

namespace App\Http\Controllers\Modules\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Strategy;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\NotificationService;
use App\Services\PermissionCacheService;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;

class StrategiesController extends Controller
{
    public function index(Request $request)
    {
        $query = Strategy::with(['status', 'responsible']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('objective', 'like', '%' . $search . '%')
                    ->orWhere('observations', 'like', '%' . $search . '%');
            });
        }

        // Filters
        if ($request->filled('status_filter')) {
            $query->where('status_id', $request->status_filter);
        }

        if ($request->filled('responsible_filter')) {
            $query->where('responsible_id', $request->responsible_filter);
        }

        if ($request->filled('platform_filter')) {
            $query->where('platforms', 'like', '%' . $request->platform_filter . '%');
        }

        if ($request->filled('target_audience_filter')) {
            $query->where('target_audience', 'like', '%' . $request->target_audience_filter . '%');
        }

        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('end_date', '<=', $request->date_to);
        }

        $strategies = $query->orderBy('created_at', 'desc')
            ->paginate($request->input('perPage', 10))
            ->withQueryString();

        // Options
        $statusCategory = TagCategory::where('slug', 'estado_estrategia')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        $responsibleOptions = User::orderBy('name')->get();

        return Inertia::render('Modules/Marketing/Strategies/Index', [
            'strategies' => $strategies,
            'filters' => $request->only([
                'search',
                'status_filter',
                'responsible_filter',
                'platform_filter',
                'target_audience_filter',
                'date_from',
                'date_to',
                'perPage'
            ]),
            'statusOptions' => $statusOptions,
            'responsibleOptions' => $responsibleOptions,
        ]);
    }

    public function create()
    {
        $statusCategory = TagCategory::where('slug', 'estado_estrategia')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        $users = User::orderBy('name')->get();

        return Inertia::render('Modules/Marketing/Strategies/Form', [
            'statusOptions' => $statusOptions,
            'users' => $users,
        ]);
    }

    public function store(Request $request, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'objective' => 'nullable|string|max:500',
            'status_id' => 'nullable|exists:tags,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'target_audience' => 'nullable|string|max:255',
            'platforms' => 'nullable|string|max:255',
            'responsible_id' => 'nullable|exists:users,id',
            'notify_team' => 'boolean',
            'add_to_calendar' => 'boolean',
            'observations' => 'nullable|string|max:1000',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $strategy = Strategy::create($validated);

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            // Obtener ID del área de marketing
            $areaId = Area::where('slug', 'marketing')->value('id');

            $folderId = null;
            if ($strategy->name) {
                $folder = $folderMaker->execute($strategy->name, null);
                $folderId = $folder->id;
            }

            $uploader->execute($files, $strategy, $folderId, $areaId);
        }

        if ($request->has('pending_file_ids')) {
            $areaId = Area::where('slug', 'marketing')->value('id');
            if ($areaId) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $strategy, $areaId);
                    }
                }
            }
        }

        if ($request->notify_team) {
            $users = PermissionCacheService::getUsersByArea('marketing');
            $notificationService = app(NotificationService::class);

            foreach ($users as $user) {
                $notificationService->createImmediate(
                    user: $user,
                    title: 'Nueva Estrategia de Marketing',
                    message: "Se ha creado la estrategia: {$strategy->name}",
                    notifiable: $strategy,
                    sendEmail: true
                );
            }
        }

        return redirect()->route('marketing.strategies.index')->with('success', 'Estrategia creada exitosamente.');
    }

    public function edit(Strategy $strategy)
    {
        $statusCategory = TagCategory::where('slug', 'estado_estrategia')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        $users = User::orderBy('name')->get();

        $strategy->load('files');

        return Inertia::render('Modules/Marketing/Strategies/Form', [
            'strategy' => $strategy,
            'statusOptions' => $statusOptions,
            'users' => $users,
        ]);
    }

    public function update(Request $request, Strategy $strategy, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'objective' => 'nullable|string|max:500',
            'status_id' => 'nullable|exists:tags,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'target_audience' => 'nullable|string|max:255',
            'platforms' => 'nullable|string|max:255',
            'responsible_id' => 'nullable|exists:users,id',
            'notify_team' => 'boolean',
            'add_to_calendar' => 'boolean',
            'observations' => 'nullable|string|max:1000',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $strategy->update($validated);

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            // Obtener ID del área de marketing
            $areaId = Area::where('slug', 'marketing')->value('id');

            $folderId = null;
            if ($strategy->name) {
                $folder = $folderMaker->execute($strategy->name, null);
                $folderId = $folder->id;
            }

            $uploader->execute($files, $strategy, $folderId, $areaId);
        }

        if ($request->has('pending_file_ids')) {
            $areaId = Area::where('slug', 'marketing')->value('id');
            if ($areaId) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $strategy, $areaId);
                    }
                }
            }
        }

        if ($request->notify_team) {
            $users = PermissionCacheService::getUsersByArea('marketing');
            $notificationService = app(NotificationService::class);

            foreach ($users as $user) {
                $notificationService->createImmediate(
                    user: $user,
                    title: 'Estrategia de Marketing Actualizada',
                    message: "Se ha actualizado la estrategia: {$strategy->name}",
                    notifiable: $strategy,
                    sendEmail: true
                );
            }
        }

        return redirect()->route('marketing.strategies.index')->with('success', 'Estrategia actualizada exitosamente.');
    }

    public function destroy(Strategy $strategy)
    {
        $strategy->delete();
        return redirect()->route('marketing.strategies.index')->with('success', 'Estrategia eliminada exitosamente.');
    }
}
