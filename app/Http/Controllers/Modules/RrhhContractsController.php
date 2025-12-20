<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\File;
use App\Actions\LinkFileAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class RrhhContractsController extends Controller
{
    public function index(Request $request)
    {
        $query = Contract::query();

        if ($request->search) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                    ->orWhere('identification_number', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status_id) {
            $query->where('status_id', $request->status_id);
        }

        $contracts = $query->with(['employee', 'type', 'category', 'status'])
            ->orderBy('created_at', 'desc')
            ->paginate($request->perPage ?? 10)
            ->withQueryString();

        $statusCategory = TagCategory::where('slug', 'estado_contrato')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return Inertia::render('Rrhh/Contracts/Index', [
            'contracts' => $contracts,
            'statusOptions' => $statusOptions,
            'filters' => $request->only(['search', 'status_id']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Rrhh/Contracts/Form', [
            'contract' => null,
            'employees' => Employee::orderBy('full_name')->get(),
            'typeOptions' => $this->getTags('tipo_contrato_contratos'),
            'categoryOptions' => $this->getTags('tipo_relacion'),
            'statusOptions' => $this->getTags('estado_contrato'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type_id' => 'required|exists:tags,id',
            'category_id' => 'required|exists:tags,id',
            'status_id' => 'required|exists:tags,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'amount' => 'nullable|numeric|min:0',
            'schedule' => 'nullable|string|max:255',
            'pending_file_ids' => 'array',
        ]);

        $validated['registered_by_id'] = Auth::id();

        $contract = Contract::create($validated);

        if (!empty($validated['pending_file_ids'])) {
            $linkFileAction = app(LinkFileAction::class);
            $linkFileAction->execute($validated['pending_file_ids'], $contract);
        }

        return redirect()->route('rrhh.contracts.index')->with('success', 'Contrato creado exitosamente.');
    }

    public function edit(Contract $contract)
    {
        return Inertia::render('Rrhh/Contracts/Form', [
            'contract' => $contract,
            'employees' => Employee::orderBy('full_name')->get(),
            'typeOptions' => $this->getTags('tipo_contrato_contratos'),
            'categoryOptions' => $this->getTags('tipo_relacion'),
            'statusOptions' => $this->getTags('estado_contrato'),
        ]);
    }

    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type_id' => 'required|exists:tags,id',
            'category_id' => 'required|exists:tags,id',
            'status_id' => 'required|exists:tags,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'amount' => 'nullable|numeric|min:0',
            'schedule' => 'nullable|string|max:255',
        ]);

        $contract->update($validated);

        return redirect()->route('rrhh.contracts.index')->with('success', 'Contrato actualizado exitosamente.');
    }

    public function show(Contract $contract)
    {
        $contract->load(['employee', 'type', 'category', 'status', 'files', 'registeredBy']);

        return Inertia::render('Rrhh/Contracts/Show', [
            'contract' => $contract,
        ]);
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();
        return redirect()->route('rrhh.contracts.index')->with('success', 'Contrato eliminado exitosamente.');
    }

    private function getTags($slug)
    {
        $category = TagCategory::where('slug', $slug)->first();
        return $category ? Tag::where('category_id', $category->id)->get() : collect();
    }
}
