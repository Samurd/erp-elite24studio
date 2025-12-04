<?php

namespace App\Livewire\Modules\Rrhh\Employees;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Livewire\Components\DepartmentManagement;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $jobTitleFilter = '';
    public $genderFilter = '';
    public $educationTypeFilter = '';
    public $maritalStatusFilter = '';
    public $departmentFilter = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'jobTitleFilter' => ['except' => ''],
        'genderFilter' => ['except' => ''],
        'educationTypeFilter' => ['except' => ''],
        'maritalStatusFilter' => ['except' => ''],
        'departmentFilter' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    protected $listeners = [
        'refresh-departments' => '$refresh',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingJobTitleFilter()
    {
        $this->resetPage();
    }

    public function updatingGenderFilter()
    {
        $this->resetPage();
    }

    public function updatingEducationTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingMaritalStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDepartmentFilter()
    {
        $this->resetPage();
    }

    public function setDepartmentFilter($departmentId)
    {
        $this->departmentFilter = $departmentId;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'jobTitleFilter',
            'genderFilter',
            'educationTypeFilter',
            'maritalStatusFilter',
            'departmentFilter',
        ]);
        $this->resetPage();
    }

    public function openDepartmentModal()
    {
        $this->dispatch('open-department-modal')->to('components.department-management-modal');
    }


    public function editDepartment($id)
    {
        $this->dispatch('edit-department-modal', $id)->to('components.department-management-modal');
    }

    public function delete($id)
    {
        $employee = Employee::find($id);

        if ($employee) {
            $employee->delete();
            session()->flash('success', 'Empleado eliminado exitosamente.');
        }
    }

    public function render()
    {
        $query = Employee::with(['gender', 'educationType', 'maritalStatus', 'department']);

        // Búsqueda general
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('work_email', 'like', '%' . $this->search . '%')
                    ->orWhere('identification_number', 'like', '%' . $this->search . '%')
                    ->orWhere('mobile_phone', 'like', '%' . $this->search . '%');
            });
        }

        // Filtro por cargo
        if ($this->jobTitleFilter) {
            $query->byJobTitle($this->jobTitleFilter);
        }

        // Filtro por género
        if ($this->genderFilter) {
            $query->byGender($this->genderFilter);
        }

        // Filtro por tipo de educación
        if ($this->educationTypeFilter) {
            $query->byEducationType($this->educationTypeFilter);
        }

        // Filtro por estado civil
        if ($this->maritalStatusFilter) {
            $query->byMaritalStatus($this->maritalStatusFilter);
        }

        // Filtro por departamento
        if ($this->departmentFilter) {
            $query->byDepartment($this->departmentFilter);
        }

        $employees = $query->paginate($this->perPage);

        // Obtener opciones para los filtros usando TagCategory
        $genderCategory = TagCategory::where('slug', 'genero')->first();
        $genders = $genderCategory ? Tag::where('category_id', $genderCategory->id)->get() : collect();

        $educationCategory = TagCategory::where('slug', 'educacion')->first();
        $educationTypes = $educationCategory ? Tag::where('category_id', $educationCategory->id)->get() : collect();

        $maritalStatusCategory = TagCategory::where('slug', 'estado_civil')->first();
        $maritalStatuses = $maritalStatusCategory ? Tag::where('category_id', $maritalStatusCategory->id)->get() : collect();

        // Obtener departamentos para los tabs con conteos
        $departments = Department::withCount('employees')->orderBy('name')->get();

        // Total de empleados activos
        $totalEmployees = Employee::count();

        return view('livewire.modules.rrhh.employees.index', [
            'employees' => $employees,
            'genders' => $genders,
            'educationTypes' => $educationTypes,
            'maritalStatuses' => $maritalStatuses,
            'departments' => $departments,
            'totalEmployees' => $totalEmployees,
        ]);
    }
}
