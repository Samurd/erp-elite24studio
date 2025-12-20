<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Induction;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RrhhInductionsController extends Controller
{
    public function index(Request $request)
    {
        $query = Induction::with([
            'employee',
            'typeBond',
            'responsible',
            'status',
            'confirmation'
        ]);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('observations', 'like', '%' . $request->search . '%')
                    ->orWhereHas('employee', function ($subQ) use ($request) {
                        $subQ->where('full_name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->employee_filter) {
            $query->where('employee_id', $request->employee_filter);
        }

        if ($request->type_bond_filter) {
            $query->where('type_bond_id', $request->type_bond_filter);
        }

        if ($request->status_filter) {
            $query->where('status_id', $request->status_filter);
        }

        if ($request->confirmation_filter) {
            $query->where('confirmation_id', $request->confirmation_filter);
        }

        if ($request->responsible_filter) {
            $query->where('responsible_id', $request->responsible_filter);
        }

        if ($request->date_from) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->entry_date_from) {
            $query->where('entry_date', '>=', $request->entry_date_from);
        }

        if ($request->entry_date_to) {
            $query->where('entry_date', '<=', $request->entry_date_to);
        }

        $inductions = $query->orderBy('created_at', 'desc')
            ->paginate($request->perPage ?? 10)
            ->withQueryString();

        return Inertia::render('Rrhh/Inductions/Index', [
            'inductions' => $inductions,
            'typeBondOptions' => $this->getTags('tipo_vinculo'),
            'statusOptions' => $this->getTags('estado_induccion'),
            'confirmationOptions' => $this->getTags('confirmacion_induccion'),
            'employees' => Employee::orderBy('full_name')->get(),
            'responsibles' => User::orderBy('name')->get(),
            'filters' => $request->all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Rrhh/Inductions/Form', [
            'induction' => null,
            'typeBondOptions' => $this->getTags('tipo_vinculo'),
            'statusOptions' => $this->getTags('estado_induccion'),
            'confirmationOptions' => $this->getTags('confirmacion_induccion'),
            'employees' => Employee::orderBy('full_name')->get(),
            'responsibles' => User::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type_bond_id' => 'nullable|exists:tags,id',
            'entry_date' => 'required|date',
            'responsible_id' => 'nullable|exists:users,id',
            'date' => 'nullable|date',
            'status_id' => 'nullable|exists:tags,id',
            'confirmation_id' => 'nullable|exists:tags,id',
            'resource' => 'nullable|string|max:255',
            'duration' => 'nullable|date_format:H:i',
            'observations' => 'nullable|string|max:1000',
        ]);

        Induction::create($validated);

        return redirect()->route('rrhh.inductions.index')->with('success', 'Inducción creada exitosamente.');
    }

    public function edit(Induction $induction)
    {
        return Inertia::render('Rrhh/Inductions/Form', [
            'induction' => $induction,
            'typeBondOptions' => $this->getTags('tipo_vinculo'),
            'statusOptions' => $this->getTags('estado_induccion'),
            'confirmationOptions' => $this->getTags('confirmacion_induccion'),
            'employees' => Employee::orderBy('full_name')->get(),
            'responsibles' => User::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Induction $induction)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'type_bond_id' => 'nullable|exists:tags,id',
            'entry_date' => 'required|date',
            'responsible_id' => 'nullable|exists:users,id',
            'date' => 'nullable|date',
            'status_id' => 'nullable|exists:tags,id',
            'confirmation_id' => 'nullable|exists:tags,id',
            'resource' => 'nullable|string|max:255',
            'duration' => 'nullable|date_format:H:i',
            'observations' => 'nullable|string|max:1000',
        ]);

        $induction->update($validated);

        return redirect()->route('rrhh.inductions.index')->with('success', 'Inducción actualizada exitosamente.');
    }

    public function destroy(Induction $induction)
    {
        $induction->delete();
        return redirect()->route('rrhh.inductions.index')->with('success', 'Inducción eliminada exitosamente.');
    }

    private function getTags($slug)
    {
        $category = TagCategory::where('slug', $slug)->first();
        return $category ? Tag::where('category_id', $category->id)->get() : collect();
    }
}
