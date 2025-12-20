<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Vacancy;
use App\Services\PermissionCacheService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RrhhVacanciesController extends Controller
{
    public function index(Request $request)
    {
        // 1. Vacancies Query
        $vacanciesQuery = Vacancy::with(['contractType', 'status', 'user'])
            ->withCount('applicants');

        if ($request->vacancySearch) {
            $vacanciesQuery->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->vacancySearch . '%')
                    ->orWhere('area', 'like', '%' . $request->vacancySearch . '%')
                    ->orWhere('description', 'like', '%' . $request->vacancySearch . '%');
            });
        }

        if ($request->contractTypeFilter) {
            $vacanciesQuery->where('contract_type_id', $request->contractTypeFilter);
        }

        if ($request->statusFilter) {
            $vacanciesQuery->where('status_id', $request->statusFilter);
        }

        $vacancies = $vacanciesQuery->orderBy('created_at', 'desc')
            ->paginate($request->vacancyPerPage ?? 10, ['*'], 'vacancies_page')
            ->withQueryString();

        // 2. Applicants Query
        $applicantsQuery = Applicant::with(['vacancy', 'status']);

        if ($request->applicantSearch) {
            $applicantsQuery->where(function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->applicantSearch . '%')
                    ->orWhere('email', 'like', '%' . $request->applicantSearch . '%')
                    ->orWhere('notes', 'like', '%' . $request->applicantSearch . '%');
            });
        }

        if ($request->applicantStatusFilter) {
            $applicantsQuery->where('status_id', $request->applicantStatusFilter);
        }

        if ($request->vacancyFilter) {
            $applicantsQuery->where('vacancy_id', $request->vacancyFilter);
        }

        $applicants = $applicantsQuery->orderBy('created_at', 'desc')
            ->paginate($request->applicantPerPage ?? 10, ['*'], 'applicants_page')
            ->withQueryString();

        // 3. Aux Data
        $vacanciesForSidebar = Vacancy::withCount('applicants')->orderBy('title')->get();
        $totalApplicants = Applicant::count();

        return Inertia::render('Rrhh/Vacancies/Index', [
            'vacancies' => $vacancies,
            'applicants' => $applicants,
            'contractTypes' => $this->getTags('tipo_contrato'),
            'vacancyStatuses' => $this->getTags('estado_vacante'),
            'applicantStatuses' => $this->getTags('estado_postulante'),
            'vacanciesForSidebar' => $vacanciesForSidebar,
            'totalApplicants' => $totalApplicants,
            'filters' => $request->all(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Rrhh/Vacancies/Form', [
            'vacancy' => null,
            'contractTypes' => $this->getTags('tipo_contrato'),
            'statuses' => $this->getTags('estado_vacante'),
            'rrhhUsers' => PermissionCacheService::getUsersByArea('rrhh'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'contract_type_id' => 'required|exists:tags,id',
            'published_at' => 'nullable|date',
            'status_id' => 'required|exists:tags,id',
            'user_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
        ]);

        Vacancy::create($validated);

        return redirect()->route('rrhh.vacancies.index')->with('success', 'Vacante creada exitosamente.');
    }

    public function edit(Vacancy $vacancy)
    {
        return Inertia::render('Rrhh/Vacancies/Form', [
            'vacancy' => $vacancy,
            'contractTypes' => $this->getTags('tipo_contrato'),
            'statuses' => $this->getTags('estado_vacante'),
            'rrhhUsers' => PermissionCacheService::getUsersByArea('rrhh'),
        ]);
    }

    public function update(Request $request, Vacancy $vacancy)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'contract_type_id' => 'required|exists:tags,id',
            'published_at' => 'nullable|date',
            'status_id' => 'required|exists:tags,id',
            'user_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $vacancy->update($validated);

        return redirect()->route('rrhh.vacancies.index')->with('success', 'Vacante actualizada exitosamente.');
    }

    public function destroy(Vacancy $vacancy)
    {
        $vacancy->delete();
        return redirect()->route('rrhh.vacancies.index')->with('success', 'Vacante eliminada exitosamente.');
    }

    private function getTags($slug)
    {
        $category = TagCategory::where('slug', $slug)->first();
        return $category ? Tag::where('category_id', $category->id)->get() : collect();
    }
}
