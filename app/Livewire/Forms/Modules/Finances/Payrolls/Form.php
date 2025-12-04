<?php

namespace App\Livewire\Forms\Modules\Finances\Payrolls;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Payroll;
use App\Services\FileUploadManager;

class Form extends LivewireForm
{
    public ?Payroll $payroll = null;

    #[Validate('nullable|exists:employees,id')]
    public $employee_id = null;

    #[Validate('required|integer|min:0')]
    public $subtotal = 0;

    #[Validate('nullable|integer|min:0')]
    public $bonos = 0;

    #[Validate('nullable|integer|min:0')]
    public $deductions = 0;

    #[Validate('required|integer')]
    public $total = 0;

    #[Validate('nullable|exists:tags,id')]
    public $status_id = null;

    #[Validate('nullable|string')]
    public $observations = '';

    public function setPayroll(Payroll $payroll)
    {
        $this->payroll = $payroll;
        $this->employee_id = $payroll->employee_id;
        $this->subtotal = $payroll->subtotal;
        $this->bonos = $payroll->bonos ?? 0;
        $this->deductions = $payroll->deductions ?? 0;
        $this->total = $payroll->total;
        $this->status_id = $payroll->status_id;
        $this->observations = $payroll->observations;
    }

    public function calculateTotal()
    {
        $subtotal = (int) $this->subtotal;
        $bonos = (int) $this->bonos;
        $deductions = (int) $this->deductions;

        $this->total = $subtotal + $bonos - $deductions;
    }

    public function store()
    {
        $this->calculateTotal();
        $this->validate();

        $payroll = Payroll::create([
            'employee_id' => $this->employee_id,
            'subtotal' => $this->subtotal,
            'bonos' => $this->bonos,
            'deductions' => $this->deductions,
            'total' => $this->total,
            'status_id' => $this->status_id,
            'observations' => $this->observations,
        ]);

        return $payroll;

    }

    public function update()
    {
        $this->calculateTotal();
        $this->validate();

        $this->payroll->update([
            'employee_id' => $this->employee_id,
            'subtotal' => $this->subtotal,
            'bonos' => $this->bonos,
            'deductions' => $this->deductions,
            'total' => $this->total,
            'status_id' => $this->status_id,
            'observations' => $this->observations,
        ]);


    }

}
