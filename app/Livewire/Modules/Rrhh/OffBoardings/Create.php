<?php

namespace App\Livewire\Modules\Rrhh\OffBoardings;

use Livewire\Component;
use App\Livewire\Forms\Modules\Rrhh\OffBoardings\Form;
use App\Models\Employee;
use App\Models\Project;
use App\Models\User;
use App\Models\Tag;
use App\Models\TagCategory;

class Create extends Component
{
    public Form $form;

    public function mount()
    {
        // Set default exit date to today
        $this->form->exit_date = date('Y-m-d');
    }

    public function save()
    {
        $this->form->store();

        session()->flash('success', 'OffBoarding creado exitosamente.');

        return redirect()->route('rrhh.offboardings.index');
    }

    public function render()
    {
        // Get employees
        $employees = Employee::orderBy('full_name')->get();

        // Get projects
        $projects = Project::orderBy('name')->get();

        // Get users for responsible dropdown
        $users = User::orderBy('name')->get();

        // Get status options
        $statusCategory = TagCategory::where('slug', 'estado_offboarding')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.rrhh.off-boardings.create', [
            'employees' => $employees,
            'projects' => $projects,
            'users' => $users,
            'statusOptions' => $statusOptions,
        ]);
    }
}
