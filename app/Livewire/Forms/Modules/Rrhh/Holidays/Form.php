<?php

namespace App\Livewire\Forms\Modules\Rrhh\Holidays;

use App\Models\Holiday;
use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;

class Form extends LivewireForm
{
    public ?Holiday $holiday = null;

    #[Validate('required|exists:employees,id')]
    public $employee_id = null;

    #[Validate('nullable|exists:tags,id')]
    public $type_id = null;

    #[Validate('required|date')]
    public $start_date = '';

    #[Validate('required|date|after_or_equal:start_date')]
    public $end_date = '';

    #[Validate('nullable|exists:tags,id')]
    public $status_id = null;

    #[Validate('nullable|exists:users,id')]
    public $approver_id = null;

    public function setHoliday(Holiday $holiday)
    {
        $this->holiday = $holiday;
        $this->employee_id = $holiday->employee_id;
        $this->type_id = $holiday->type_id;
        $this->start_date = $holiday->start_date->format('Y-m-d');
        $this->end_date = $holiday->end_date->format('Y-m-d');
        $this->status_id = $holiday->status_id;
        $this->approver_id = $holiday->approver_id;
    }

    public function store()
    {
        $this->validate();

        $holiday = Holiday::create([
            'employee_id' => $this->employee_id,
            'type_id' => $this->type_id ?: null,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status_id' => $this->status_id ?: null,
            'approver_id' => $this->approver_id ?: null,
        ]);

        return $holiday;
    }

    public function update()
    {
        $this->validate();

        $this->holiday->update([
            'employee_id' => $this->employee_id,
            'type_id' => $this->type_id ?: null,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status_id' => $this->status_id ?: null,
            'approver_id' => $this->approver_id ?: null,
        ]);
    }
}
