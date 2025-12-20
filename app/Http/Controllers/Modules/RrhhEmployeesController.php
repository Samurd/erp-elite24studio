<?php

namespace App\Http\Controllers\Modules;

use App\Actions\Cloud\Files\LinkFileAction;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RrhhEmployeesController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with(['gender', 'educationType', 'maritalStatus', 'department']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                    ->orWhere('work_email', 'like', '%' . $request->search . '%')
                    ->orWhere('identification_number', 'like', '%' . $request->search . '%')
                    ->orWhere('mobile_phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->job_title) {
            $query->where('job_title', 'like', '%' . $request->job_title . '%');
        }

        // Additional filters matching Livewire implementation
        if ($request->gender_id) {
            $query->where('gender_id', $request->gender_id);
        }
        if ($request->education_type_id) {
            $query->where('education_type_id', $request->education_type_id);
        }
        if ($request->marital_status_id) {
            $query->where('marital_status_id', $request->marital_status_id);
        }

        $employees = $query->orderBy('created_at', 'desc')
            ->paginate($request->perPage ?? 10)
            ->withQueryString();

        $departments = Department::withCount('employees')->orderBy('name')->get();
        $totalEmployees = Employee::count();

        return Inertia::render('Rrhh/Employees/Index', [
            'employees' => $employees,
            'departments' => $departments,
            'totalEmployees' => $totalEmployees,
            'filters' => $request->only(['search', 'department_id', 'job_title', 'gender_id', 'education_type_id', 'marital_status_id']),
            'genderOptions' => $this->getTags('genero'),
            'educationOptions' => $this->getTags('educacion'),
            'maritalStatusOptions' => $this->getTags('estado_civil'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Rrhh/Employees/Form', [
            'employee' => null,
            'departments' => Department::orderBy('name')->get(),
            'genderOptions' => $this->getTags('genero'),
            'educationOptions' => $this->getTags('educacion'),
            'maritalStatusOptions' => $this->getTags('estado_civil'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        $employee = Employee::create($validated);

        if (!empty($request->pending_file_ids)) {
            $linkFileAction = app(LinkFileAction::class);
            $linkFileAction->execute($request->pending_file_ids, $employee);
        }

        return redirect()->route('rrhh.employees.index')->with('success', 'Empleado creado exitosamente.');
    }

    public function edit(Employee $employee)
    {
        $employee->load(['files']); // Load files for ModelAttachments

        return Inertia::render('Rrhh/Employees/Form', [
            'employee' => $employee,
            'departments' => Department::orderBy('name')->get(),
            'genderOptions' => $this->getTags('genero'),
            'educationOptions' => $this->getTags('educacion'),
            'maritalStatusOptions' => $this->getTags('estado_civil'),
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate($this->rules($employee->id));

        $employee->update($validated);

        return redirect()->route('rrhh.employees.index')->with('success', 'Empleado actualizado exitosamente.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('rrhh.employees.index')->with('success', 'Empleado eliminado exitosamente.');
    }

    private function rules($employeeId = null)
    {
        return [
            // Work Information
            'full_name' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'work_email' => 'required|email|unique:employees,work_email,' . $employeeId,
            'mobile_phone' => 'required|string|max:20',
            'work_address' => 'required|string|max:500',
            'work_schedule' => 'required|string|max:100',
            'department_id' => 'required|exists:departments,id',

            // Private Information
            'home_address' => 'nullable|string|max:500',
            'personal_email' => 'nullable|email|max:255',
            'private_phone' => 'nullable|string|max:20',
            'bank_account' => 'nullable|string|max:50',
            'identification_number' => 'required|string|max:50|unique:employees,identification_number,' . $employeeId,
            'social_security_number' => 'nullable|string|max:50',
            'passport_number' => 'nullable|string|max:50',
            'gender_id' => 'nullable|exists:tags,id',
            'birth_date' => 'nullable|date|before:today',
            'birth_place' => 'nullable|string|max:255',
            'birth_country' => 'nullable|string|max:255',
            'has_disability' => 'boolean',
            'disability_details' => 'required_if:has_disability,true|nullable|string|max:500',

            // Emergency Contact
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20',

            // Education
            'education_type_id' => 'nullable|exists:tags,id',

            // Family Status
            'marital_status_id' => 'nullable|exists:tags,id',
            'number_of_dependents' => 'required|integer|min:0|max:20',

            // Files
            'pending_file_ids' => 'array',
        ];
    }

    private function getTags($slug)
    {
        $category = TagCategory::where('slug', $slug)->first();
        return $category ? Tag::where('category_id', $category->id)->get() : collect();
    }
}
