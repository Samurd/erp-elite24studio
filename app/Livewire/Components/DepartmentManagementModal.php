<?php

namespace App\Livewire\Components;

use App\Models\Department;
use Livewire\Component;

class DepartmentManagementModal extends Component
{
    public $showModal = false;
    public $departments;
    public $departmentName = '';
    public $departmentDescription = '';
    public $departmentParentId = null;
    public $editingDepartmentId = null;

    protected $listeners = [
        'refresh-departments' => 'loadDepartments',
        'open-department-modal' => 'openDepartmentModal',
        'open-department-management-modal' => 'openDepartmentManagementModal',
        'edit-department-modal' => 'editDepartment',
        'close-modal' => 'closeModal',
    ];

    public function mount()
    {
        $this->showModal = false;
        $this->loadDepartments();
    }

    public function loadDepartments()
    {
        $this->departments = Department::withCount('employees')->orderBy('name')->get();
    }

    public function openDepartmentModal()
    {
        $this->showModal = true;
    }

    public function openDepartmentManagementModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function createDepartment()
    {
        $this->validate([
            'departmentName' => 'required|string|max:255',
            'departmentDescription' => 'nullable|string|max:500',
            'departmentParentId' => 'nullable|exists:departments,id',
        ]);

        try {
            $department = Department::create([
                'name' => $this->departmentName,
                'description' => $this->departmentDescription,
                'parent_id' => $this->departmentParentId ?: null,
            ]);

            $this->resetDepartmentForm();
            $this->dispatch('show-notification', [
                'type' => 'success',
                'message' => 'Departamento creado exitosamente.',
            ]);

            // Refresh departments list
            $this->dispatch('refresh-departments');
        } catch (\Exception $e) {
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Error al crear departamento: ' . $e->getMessage(),
            ]);
        }
    }

    public function editDepartment($id)
    {
        $department = Department::find($id);

        if (!$department) {
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Departamento no encontrado.',
            ]);
            return;
        }

        $this->editingDepartmentId = $id;
        $this->departmentName = $department->name;
        $this->departmentDescription = $department->description;
        $this->departmentParentId = $department->parent_id;

        // Open the modal after setting the values
        $this->showModal = true;
    }

    public function updateDepartment()
    {
        $this->validate([
            'departmentName' => 'required|string|max:255',
            'departmentDescription' => 'nullable|string|max:500',
            'departmentParentId' => 'nullable|exists:departments,id',
        ]);

        try {
            $department = Department::find($this->editingDepartmentId);

            if ($department) {
                $department->update([
                    'name' => $this->departmentName,
                    'description' => $this->departmentDescription,
                    'parent_id' => $this->departmentParentId ?: null,
                ]);

                $this->resetDepartmentForm();
                $this->dispatch('show-notification', [
                    'type' => 'success',
                    'message' => 'Departamento actualizado exitosamente.',
                ]);

                // Refresh departments list
                $this->dispatch('refresh-departments');
            }
        } catch (\Exception $e) {
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Error al actualizar departamento: ' . $e->getMessage(),
            ]);
        }
    }

    public function deleteDepartment($id)
    {
        try {
            $department = Department::find($id);

            if (!$department) {
                $this->dispatch('show-notification', [
                    'type' => 'error',
                    'message' => 'Departamento no encontrado.',
                ]);
                return;
            }

            // Check if department has employees
            $employeeCount = $department->employees()->count();

            if ($employeeCount > 0) {
                $this->dispatch('show-notification', [
                    'type' => 'error',
                    'message' => 'No se puede eliminar el departamento porque tiene ' . $employeeCount . ' empleado(s) asignado(s).',
                ]);
                return;
            }

            $department->delete();

            $this->dispatch('show-notification', [
                'type' => 'success',
                'message' => 'Departamento eliminado exitosamente.',
            ]);

            // Refresh departments list
            $this->dispatch('refresh-departments');
        } catch (\Exception $e) {
            $this->dispatch('show-notification', [
                'type' => 'error',
                'message' => 'Error al eliminar departamento: ' . $e->getMessage(),
            ]);
        }
    }

    public function resetDepartmentForm()
    {
        $this->editingDepartmentId = null;
        $this->departmentName = '';
        $this->departmentDescription = '';
        $this->departmentParentId = null;
    }

    public function render()
    {
        return view('livewire.components.department-management-modal');
    }
}