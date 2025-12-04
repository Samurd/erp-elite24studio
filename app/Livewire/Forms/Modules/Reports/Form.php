<?php

namespace App\Livewire\Forms\Modules\Reports;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Report;

class Form extends LivewireForm
{
    public ?Report $report = null;

    #[Validate('required|string|max:255')]
    public $title = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('nullable|exists:tags,id')]
    public $status_id = null;

    #[Validate('nullable|date')]
    public $date = '';

    #[Validate('nullable')]
    public $hour = '';

    #[Validate('nullable|exists:users,id')]
    public $user_id = null;

    #[Validate('nullable|string')]
    public $notes = '';

    public function setReport(Report $report)
    {
        $this->report = $report;
        $this->title = $report->title;
        $this->description = $report->description;
        $this->status_id = $report->status_id;
        $this->date = $report->date ? $report->date->format('Y-m-d') : '';
        $this->hour = $report->hour ? \Carbon\Carbon::parse($report->hour)->format('H:i') : '';
        $this->user_id = $report->user_id;
        $this->notes = $report->notes;
    }

    public function store()
    {
        $this->validate();

        $report = Report::create([
            'title' => $this->title,
            'description' => $this->description,
            'status_id' => $this->status_id,
            'date' => $this->date,
            'hour' => $this->hour,
            'user_id' => $this->user_id ?? auth()->id(),
            'notes' => $this->notes,
        ]);

        return $report;

    }

    public function update()
    {
        $this->validate();

        $this->report->update([
            'title' => $this->title,
            'description' => $this->description,
            'status_id' => $this->status_id,
            'date' => $this->date,
            'hour' => $this->hour,
            'user_id' => $this->user_id ?? auth()->id(),
            'notes' => $this->notes,
        ]);
    }
}
