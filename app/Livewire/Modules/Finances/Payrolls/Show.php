<?php

namespace App\Livewire\Modules\Finances\Payrolls;

use Livewire\Component;

use App\Models\Payroll;

class Show extends Component
{
    public Payroll $payroll;

    public function mount(Payroll $payroll)
    {
        $this->payroll = $payroll;
    }

    public function render()
    {
        return view('livewire.modules.finances.payrolls.show');
    }
}
