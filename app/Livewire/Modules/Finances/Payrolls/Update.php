<?php

namespace App\Livewire\Modules\Finances\Payrolls;

use Livewire\Component;
use Livewire\WithFileUploads;

use App\Livewire\Forms\Modules\Finances\Payrolls\Form;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Tag;
use App\Models\TagCategory;

class Update extends Component
{
    use WithFileUploads;

    public Form $form;
    public Payroll $payroll;



    public function mount(Payroll $payroll)
    {
        $this->payroll = $payroll;
        $this->form->setPayroll($payroll);
    }

    public function save()
    {

        $this->form->update();

        session()->flash('success', 'Nómina actualizada exitosamente.');

        return redirect()->route('finances.payrolls.index');
    }

    public function render()
    {
        $employees = Employee::active()->orderBy('full_name')->get();

        $statusCategory = TagCategory::where('slug', 'estado_nomina')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.finances.payrolls.create', [
            'employees' => $employees,
            'statusOptions' => $statusOptions,
            'isEdit' => true,
        ]);
    }
}
