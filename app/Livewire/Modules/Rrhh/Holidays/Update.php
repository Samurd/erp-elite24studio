<?php

namespace App\Livewire\Modules\Rrhh\Holidays;

use App\Livewire\Forms\Modules\Rrhh\Holidays\Form;
use App\Models\Holiday;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    public Holiday $holiday;

    public function mount(Holiday $holiday)
    {
        $this->holiday = $holiday;
        $this->form->setHoliday($holiday);
    }

    public function save()
    {
        $this->form->update();

        session()->flash('success', 'Festivo actualizado exitosamente');

        return redirect()->route('rrhh.holidays.index');
    }

    public function render()
    {
        // Get type options
        $typeCategory = TagCategory::where('slug', 'tipo_vacacion')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        // Get status options
        $statusCategory = TagCategory::where('slug', 'estado_vacacion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Get users
        $users = User::orderBy('name')->get();

        // Get employees
        $employees = \App\Models\Employee::orderBy('full_name')->get();

        return view('livewire.modules.rrhh.holidays.create', [
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'users' => $users,
            'employees' => $employees,
        ]);
    }
}
