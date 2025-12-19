<?php

namespace App\Http\Controllers\Modules\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventItem;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use App\Models\Area;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;

class EventsController extends Controller
{
    // --- Events Methods ---

    public function index(Request $request)
    {
        $query = Event::with(['type', 'status', 'responsible', 'items']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('location', 'like', '%' . $search . '%');
            });
        }

        // Filters
        if ($request->filled('type_filter')) {
            $query->where('type_id', $request->type_filter);
        }

        if ($request->filled('status_filter')) {
            $query->where('status_id', $request->status_filter);
        }

        if ($request->filled('responsible_filter')) {
            $query->where('responsible_id', $request->responsible_filter);
        }

        if ($request->filled('date_from')) {
            $query->where('event_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('event_date', '<=', $request->date_to);
        }

        $events = $query->orderBy('event_date', 'desc')
            ->paginate($request->input('perPage', 10))
            ->withQueryString();

        // Calculate total budget (sum of items total_price)
        // Note: For pagination, this should ideally be a separate sum query or sum of the current page.
        // Livewire implemented it as sum of current collection. We'll do the same for consistency.
        $totalPresupuesto = $events->getCollection()->sum(function ($event) {
            return $event->items->sum('total_price');
        });

        // Options
        $eventTypeCategory = TagCategory::where('slug', 'tipo_evento')->first();
        $eventTypeOptions = $eventTypeCategory ? Tag::where('category_id', $eventTypeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_evento')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $responsibleOptions = User::orderBy('name')->get();

        return Inertia::render('Modules/Marketing/Events/Index', [
            'events' => $events,
            'filters' => $request->only([
                'search',
                'type_filter',
                'status_filter',
                'responsible_filter',
                'date_from',
                'date_to',
                'perPage'
            ]),
            'eventTypeOptions' => $eventTypeOptions,
            'statusOptions' => $statusOptions,
            'responsibleOptions' => $responsibleOptions,
            'totalPresupuesto' => $totalPresupuesto,
        ]);
    }

    public function create()
    {
        $eventTypeCategory = TagCategory::where('slug', 'tipo_evento')->first();
        $eventTypeOptions = $eventTypeCategory ? Tag::where('category_id', $eventTypeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_evento')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $responsibleOptions = User::orderBy('name')->get();

        return Inertia::render('Modules/Marketing/Events/Form', [
            'eventTypeOptions' => $eventTypeOptions,
            'statusOptions' => $statusOptions,
            'responsibleOptions' => $responsibleOptions,
        ]);
    }

    public function store(Request $request, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:tags,id',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'status_id' => 'required|exists:tags,id',
            'responsible_id' => 'required|exists:users,id',
            'observations' => 'nullable|string',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $event = Event::create($validated);

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            // Obtener ID del área de marketing
            $areaId = Area::where('slug', 'marketing')->value('id');

            $folderId = null;
            if ($event->name) {
                $folder = $folderMaker->execute($event->name, null);
                $folderId = $folder->id;
            }

            $uploader->execute($files, $event, $folderId, $areaId);
        }

        if ($request->has('pending_file_ids')) {
            $areaId = Area::where('slug', 'marketing')->value('id');
            if ($areaId) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $event, $areaId);
                    }
                }
            }
        }

        return redirect()->route('marketing.events.index')->with('success', 'Evento creado exitosamente.');
    }

    public function show(Event $event, Request $request)
    {
        $event->load(['type', 'status', 'responsible', 'files']);

        $itemsQuery = $event->items()->with('unit');

        if ($request->filled('search')) {
            $itemsQuery->where('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('unitFilter')) {
            $itemsQuery->where('unit_id', $request->unitFilter);
        }

        $items = $itemsQuery->paginate($request->input('perPage', 10))->withQueryString();

        $unitCategory = TagCategory::where('slug', 'unidad')->first();
        $unitOptions = $unitCategory ? Tag::where('category_id', $unitCategory->id)->get() : collect();

        return Inertia::render('Modules/Marketing/Events/Show', [
            'event' => $event,
            'items' => $items,
            'unitOptions' => $unitOptions,
            'filters' => $request->only(['search', 'unitFilter', 'perPage']),
        ]);
    }

    public function edit(Event $event)
    {
        $eventTypeCategory = TagCategory::where('slug', 'tipo_evento')->first();
        $eventTypeOptions = $eventTypeCategory ? Tag::where('category_id', $eventTypeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_evento')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $responsibleOptions = User::orderBy('name')->get();

        $event->load('files');

        return Inertia::render('Modules/Marketing/Events/Form', [
            'event' => $event,
            'eventTypeOptions' => $eventTypeOptions,
            'statusOptions' => $statusOptions,
            'responsibleOptions' => $responsibleOptions,
        ]);
    }

    public function update(Request $request, Event $event, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type_id' => 'required|exists:tags,id',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'status_id' => 'required|exists:tags,id',
            'responsible_id' => 'required|exists:users,id',
            'observations' => 'nullable|string',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $event->update($validated);

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            // Obtener ID del área de marketing
            $areaId = Area::where('slug', 'marketing')->value('id');

            $folderId = null;
            if ($event->name) {
                $folder = $folderMaker->execute($event->name, null);
                $folderId = $folder->id;
            }

            $uploader->execute($files, $event, $folderId, $areaId);
        }

        if ($request->has('pending_file_ids')) {
            $areaId = Area::where('slug', 'marketing')->value('id');
            if ($areaId) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $event, $areaId);
                    }
                }
            }
        }

        return redirect()->route('marketing.events.index')->with('success', 'Evento actualizado exitosamente.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('marketing.events.index')->with('success', 'Evento eliminado exitosamente.');
    }

    // --- Event Items Methods ---

    public function createItem(Event $event)
    {
        $unitCategory = TagCategory::where('slug', 'unidad')->first();
        $unitOptions = $unitCategory ? Tag::where('category_id', $unitCategory->id)->get() : collect();

        return Inertia::render('Modules/Marketing/Events/Items/Form', [
            'event' => $event,
            'unitOptions' => $unitOptions,
        ]);
    }

    public function storeItem(Request $request, Event $event, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit_id' => 'required|exists:tags,id',
            'unit_price' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $validated['event_id'] = $event->id;

        $item = EventItem::create($validated);

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            // Obtener ID del área de marketing
            $areaId = Area::where('slug', 'marketing')->value('id');

            $folderId = null;
            if ($event->name) {
                $folder = $folderMaker->execute($event->name, null);
                $folderId = $folder->id;
            }

            $uploader->execute($files, $item, $folderId, $areaId);
        }

        if ($request->has('pending_file_ids')) {
            $areaId = Area::where('slug', 'marketing')->value('id');
            if ($areaId) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $item, $areaId);
                    }
                }
            }
        }

        return redirect()->route('marketing.events.show', $event->id)->with('success', 'Ítem agregado exitosamente.');
    }

    public function editItem(Event $event, EventItem $eventItem)
    {
        $unitCategory = TagCategory::where('slug', 'unidad')->first();
        $unitOptions = $unitCategory ? Tag::where('category_id', $unitCategory->id)->get() : collect();

        $event = $eventItem->event;

        $eventItem->load('files');

        return Inertia::render('Modules/Marketing/Events/Items/Form', [
            'event' => $event,
            'item' => $eventItem,
            'unitOptions' => $unitOptions,
        ]);
    }

    public function updateItem(Request $request, Event $event, EventItem $eventItem, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'unit_id' => 'required|exists:tags,id',
            'unit_price' => 'required|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $eventItem->update($validated);

        $event = $eventItem->event;

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            // Obtener ID del área de marketing
            $areaId = Area::where('slug', 'marketing')->value('id');

            $folderId = null;
            if ($event->name) {
                $folder = $folderMaker->execute($event->name, null);
                $folderId = $folder->id;
            }

            $uploader->execute($files, $eventItem, $folderId, $areaId);
        }

        if ($request->has('pending_file_ids')) {
            $areaId = Area::where('slug', 'marketing')->value('id');
            if ($areaId) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $eventItem, $areaId);
                    }
                }
            }
        }

        return redirect()->route('marketing.events.show', $eventItem->event_id)->with('success', 'Ítem actualizado exitosamente.');
    }

    public function destroyItem(Event $event, EventItem $eventItem)
    {
        $eventId = $eventItem->event_id;
        $eventItem->delete();
        return redirect()->back()->with('success', 'Ítem eliminado exitosamente.');
    }
}
