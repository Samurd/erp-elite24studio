<?php

namespace App\Livewire\Forms\Modules\Rrhh\Attendances;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Attendance;

class Form extends LivewireForm
{
    public ?Attendance $attendance = null;

    #[Validate('required|exists:employees,id')]
    public $employee_id = null;

    #[Validate('required|date')]
    public $date = '';

    #[Validate('required|date_format:H:i')]
    public $check_in = '';

    #[Validate('required|date_format:H:i')]
    public $check_out = '';

    #[Validate('nullable|exists:tags,id')]
    public $status_id = null;

    #[Validate('nullable|exists:tags,id')]
    public $modality_id = null;

    #[Validate('nullable|string')]
    public $observations = '';

    public function setAttendance(Attendance $attendance)
    {
        $this->attendance = $attendance;
        $this->employee_id = $attendance->employee_id;
        $this->date = $attendance->date ? $attendance->date->format('Y-m-d') : '';
        $this->check_in = $attendance->check_in ? $attendance->check_in->format('H:i') : '';
        $this->check_out = $attendance->check_out ? $attendance->check_out->format('H:i') : '';
        $this->status_id = $attendance->status_id;
        $this->modality_id = $attendance->modality_id;
        $this->observations = $attendance->observations;
    }

    public function store()
    {
        $this->validate();

        Attendance::create([
            'employee_id' => $this->employee_id,
            'date' => $this->date,
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'status_id' => $this->status_id,
            'modality_id' => $this->modality_id,
            'observations' => $this->observations,
        ]);
    }

    public function update()
    {
        $this->validate();

        $this->attendance->update([
            'employee_id' => $this->employee_id,
            'date' => $this->date,
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'status_id' => $this->status_id,
            'modality_id' => $this->modality_id,
            'observations' => $this->observations,
        ]);
    }
}
