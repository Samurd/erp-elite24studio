<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RrhhApplicantsController extends Controller
{
    public function create()
    {
        return Inertia::render('Rrhh/Applicants/Form', [
            'applicant' => null,
            'vacancies' => Vacancy::orderBy('title')->get(),
            'statusOptions' => $this->getTags('estado_postulante'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vacancy_id' => 'required|exists:vacancies,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'status_id' => 'required|exists:tags,id',
            'notes' => 'nullable|string',
        ]);

        Applicant::create($validated);

        return redirect()->route('rrhh.vacancies.index')->with('success', 'Postulante creado exitosamente.');
    }

    public function edit(Applicant $applicant)
    {
        return Inertia::render('Rrhh/Applicants/Form', [
            'applicant' => $applicant,
            'vacancies' => Vacancy::orderBy('title')->get(),
            'statusOptions' => $this->getTags('estado_postulante'),
        ]);
    }

    public function update(Request $request, Applicant $applicant)
    {
        $validated = $request->validate([
            'vacancy_id' => 'required|exists:vacancies,id',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'status_id' => 'required|exists:tags,id',
            'notes' => 'nullable|string',
        ]);

        $applicant->update($validated);

        return redirect()->route('rrhh.vacancies.index')->with('success', 'Postulante actualizado exitosamente.');
    }

    private function getTags($slug)
    {
        $category = TagCategory::where('slug', $slug)->first();
        return $category ? Tag::where('category_id', $category->id)->get() : collect();
    }
}
