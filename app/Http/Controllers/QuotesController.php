<?php

namespace App\Http\Controllers;

use App\Actions\Cloud\Files\UploadFileAction;
use App\Actions\Cloud\Folders\GetOrCreateFolderAction;
use App\Models\Area;
use App\Models\Quote;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class QuotesController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        // Permission check middleware handles access, but can add explicit check if needed
        // $this->authorize('viewAny', Quote::class); 

        $search = $request->input('search');
        $statusFilter = $request->input('status_filter');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $perPage = $request->input('perPage', 10);

        $query = Quote::with(['contact', 'status', 'user', 'files']);

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                    ->orWhereHas('contact', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Filters
        if ($statusFilter) {
            $query->where('status_id', $statusFilter);
        }

        if ($dateFrom) {
            $query->whereDate('issued_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('issued_at', '<=', $dateTo);
        }

        $quotes = $query->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString()
            ->through(function ($quote) {
                return [
                    'id' => $quote->id,
                    'contact' => $quote->contact ? $quote->contact->only(['id', 'name']) : null,
                    'issued_at' => $quote->issued_at,
                    'formatted_date' => $quote->issued_at ? \Carbon\Carbon::parse($quote->issued_at)->format('d/m/Y') : '-',
                    'status' => $quote->status ? $quote->status->only(['id', 'name', 'color', 'class']) : null, // Assuming status has these
                    'total' => $quote->total,
                    'formatted_total' => \App\Services\MoneyFormatterService::format($quote->total),
                    'files_count' => $quote->files->count(),
                    // Add other needed fields
                ];
            });

        // Status Options
        $statusCategory = TagCategory::where('slug', 'estado_cotizacion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get(['id', 'name']) : [];

        return Inertia::render('Quotes/Index', [
            'quotes' => $quotes,
            'statusOptions' => $statusOptions,
            'filters' => [
                'search' => $search,
                'status_filter' => $statusFilter,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'perPage' => $perPage,
            ],
        ]);
    }

    public function create()
    {
        $contacts = \App\Models\Contact::orderBy('name')->get(['id', 'name']);
        $statusCategory = TagCategory::where('slug', 'estado_cotizacion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get(['id', 'name']) : [];

        return Inertia::render('Quotes/Form', [
            'contacts' => $contacts,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function store(Request $request, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $validated = $request->validate([
            'contact_id' => 'nullable|exists:contacts,id',
            'issued_at' => 'required|date',
            'status_id' => 'nullable|exists:tags,id',
            'total' => 'nullable|integer|min:0',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $quote = Quote::create([
            'contact_id' => $validated['contact_id'],
            'issued_at' => $validated['issued_at'],
            'status_id' => $validated['status_id'],
            'total' => $validated['total'],
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
        ]);

        if ($request->hasFile('files')) {
            $area = Area::where('slug', 'cotizaciones')->first();
            // Create a folder for the quote if it doesn't exist. Name convention: "Cotización #{id}" ? 
            // Or use "Cotizaciones" root? Livewire Create.php used "Cotizacion id: {$quote->id}"
            if ($area) {
                $folder = $folderMaker->execute('Cotizaciones/Cotización #' . $quote->id, null);
                $uploader->execute($request->file('files'), $quote, $folder->id, $area->id);
            }
        }

        if ($request->has('pending_file_ids')) {
            $area = Area::where('slug', 'cotizaciones')->first();
            if ($area) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $quote, $area->id);
                    }
                }
            }
        }

        return redirect()->route('quotes.edit', $quote->id)->with('success', 'Cotización creada exitosamente.');
    }

    public function edit($id)
    {
        $quote = Quote::with(['contact', 'files', 'status'])->findOrFail($id);

        $contacts = \App\Models\Contact::orderBy('name')->get(['id', 'name']);
        $statusCategory = TagCategory::where('slug', 'estado_cotizacion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get(['id', 'name']) : [];

        return Inertia::render('Quotes/Form', [
            'quote' => $quote,
            'contacts' => $contacts,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function update(Request $request, $id, UploadFileAction $uploader, GetOrCreateFolderAction $folderMaker, \App\Actions\Cloud\Files\LinkFileAction $linker)
    {
        $quote = Quote::findOrFail($id);

        $validated = $request->validate([
            'contact_id' => 'nullable|exists:contacts,id',
            'issued_at' => 'required|date',
            'status_id' => 'nullable|exists:tags,id',
            'total' => 'nullable|integer|min:0',
            'files.*' => 'file|max:51200',
            'pending_file_ids' => 'nullable|array',
            'pending_file_ids.*' => 'integer|exists:files,id',
        ]);

        $quote->update([
            'contact_id' => $validated['contact_id'],
            'issued_at' => $validated['issued_at'],
            'status_id' => $validated['status_id'],
            'total' => $validated['total'],
        ]);

        if ($request->hasFile('files')) {
            $area = Area::where('slug', 'cotizaciones')->first();
            if ($area) {
                $folder = $folderMaker->execute('Cotizaciones/Cotización #' . $quote->id, null);
                $uploader->execute($request->file('files'), $quote, $folder->id, $area->id);
            }
        }

        if ($request->has('pending_file_ids')) {
            $area = Area::where('slug', 'cotizaciones')->first();
            if ($area) {
                foreach ($request->pending_file_ids as $fileId) {
                    $file = \App\Models\File::find($fileId);
                    if ($file) {
                        $linker->execute($file, $quote, $area->id);
                    }
                }
            }
        }

        return back()->with('success', 'Cotización actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $quote = Quote::findOrFail($id);
        $quote->delete();
        return redirect()->route('quotes.index')->with('success', 'Cotización eliminada exitosamente.');
    }
}
