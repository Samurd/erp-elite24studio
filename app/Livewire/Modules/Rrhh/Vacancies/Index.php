<?php

namespace App\Livewire\Modules\Rrhh\Vacancies;

use App\Models\Applicant;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Vacancy;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    // Tab principal activa
    public $activeTab = 'vacancies';

    // Filtros para vacantes
    public $vacancySearch = '';
    public $contractTypeFilter = '';
    public $statusFilter = '';
    public $vacancyPerPage = 10;

    // Filtros para postulantes
    public $applicantSearch = '';
    public $applicantStatusFilter = '';
    public $vacancyFilter = '';
    public $applicantPerPage = 10;

    protected $queryString = [
        'activeTab' => ['except' => 'vacancies'],
        'vacancySearch' => ['except' => ''],
        'contractTypeFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'vacancyPerPage' => ['except' => 10],
        'applicantSearch' => ['except' => ''],
        'applicantStatusFilter' => ['except' => ''],
        'vacancyFilter' => ['except' => ''],
        'applicantPerPage' => ['except' => 10],
    ];

    // Métodos para actualizar paginación cuando cambian los filtros
    public function updatingVacancySearch()
    {
        $this->resetPage();
    }

    public function updatingContractTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingApplicantSearch()
    {
        $this->resetPage();
    }

    public function updatingApplicantStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingVacancyFilter()
    {
        $this->resetPage();
    }

    // Métodos para cambiar de tab
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // Método para seleccionar una vacante desde el sidebar
    public function setVacancyFilter($vacancyId)
    {
        $this->vacancyFilter = $vacancyId;
        $this->resetPage();
    }

    // Métodos para limpiar filtros
    public function clearVacancyFilters()
    {
        $this->reset([
            'vacancySearch',
            'contractTypeFilter',
            'statusFilter',
        ]);
        $this->resetPage();
    }

    public function clearApplicantFilters()
    {
        $this->reset([
            'applicantSearch',
            'applicantStatusFilter',
            'vacancyFilter',
        ]);
        $this->resetPage();
    }

    public function render()
    {
        // Obtener opciones para los filtros usando TagCategory
        $contractCategory = TagCategory::where('slug', 'tipo_contrato')->first();
        $contractTypes = $contractCategory ? Tag::where('category_id', $contractCategory->id)->get() : collect();

        $vacancyStatusCategory = TagCategory::where('slug', 'estado_vacante')->first();
        $vacancyStatuses = $vacancyStatusCategory ? Tag::where('category_id', $vacancyStatusCategory->id)->get() : collect();

        $applicantStatusCategory = TagCategory::where('slug', 'estado_postulante')->first();
        $applicantStatuses = $applicantStatusCategory ? Tag::where('category_id', $applicantStatusCategory->id)->get() : collect();

        // Consulta para vacantes
        $vacanciesQuery = Vacancy::with(['contractType', 'status', 'user'])
                                ->withCount('applicants');

        if ($this->vacancySearch) {
            $vacanciesQuery->where(function ($q) {
                $q->where('title', 'like', '%' . $this->vacancySearch . '%')
                  ->orWhere('area', 'like', '%' . $this->vacancySearch . '%')
                  ->orWhere('description', 'like', '%' . $this->vacancySearch . '%');
            });
        }

        if ($this->contractTypeFilter) {
            $vacanciesQuery->where('contract_type_id', $this->contractTypeFilter);
        }

        if ($this->statusFilter) {
            $vacanciesQuery->where('status_id', $this->statusFilter);
        }

        $vacancies = $vacanciesQuery->orderBy('created_at', 'desc')->paginate($this->vacancyPerPage);

        // Obtener vacantes para el sidebar con conteos de postulantes
        $vacanciesForSidebar = Vacancy::withCount('applicants')->orderBy('title')->get();

        // Consulta para postulantes
        $applicantsQuery = Applicant::with(['vacancy', 'status']);

        if ($this->applicantSearch) {
            $applicantsQuery->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->applicantSearch . '%')
                  ->orWhere('email', 'like', '%' . $this->applicantSearch . '%')
                  ->orWhere('notes', 'like', '%' . $this->applicantSearch . '%');
            });
        }

        if ($this->applicantStatusFilter) {
            $applicantsQuery->where('status_id', $this->applicantStatusFilter);
        }

        if ($this->vacancyFilter) {
            $applicantsQuery->where('vacancy_id', $this->vacancyFilter);
        }

        $applicants = $applicantsQuery->orderBy('created_at', 'desc')->paginate($this->applicantPerPage);

        // Total de postulantes
        $totalApplicants = Applicant::count();

        return view('livewire.modules.rrhh.vacancies.index', [
            'vacancies' => $vacancies,
            'applicants' => $applicants,
            'contractTypes' => $contractTypes,
            'vacancyStatuses' => $vacancyStatuses,
            'applicantStatuses' => $applicantStatuses,
            'vacanciesForSidebar' => $vacanciesForSidebar,
            'totalApplicants' => $totalApplicants,
        ]);
    }
}
