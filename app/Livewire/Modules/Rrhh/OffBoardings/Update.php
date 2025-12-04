<?php

namespace App\Livewire\Modules\Rrhh\OffBoardings;

use Livewire\Component;
use App\Livewire\Forms\Modules\Rrhh\OffBoardings\Form;
use App\Models\OffBoarding;
use App\Models\Employee;
use App\Models\Project;
use App\Models\User;
use App\Models\Tag;
use App\Models\TagCategory;

class Update extends Component
{
    public Form $form;
    public OffBoarding $offboarding;

    public function mount(OffBoarding $offboarding)
    {
        $this->offboarding = $offboarding;
        $this->form->setOffBoarding($offboarding);
    }

    public function update()
    {
        $this->form->update();

        session()->flash('success', 'OffBoarding actualizado exitosamente.');

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

        return view('livewire.modules.rrhh.off-boardings.update', [
            'employees' => $employees,
            'projects' => $projects,
            'users' => $users,
            'statusOptions' => $statusOptions,
        ]);
    }
}
