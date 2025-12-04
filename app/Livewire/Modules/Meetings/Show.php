<?php

namespace App\Livewire\Modules\Meetings;

use Livewire\Component;
use App\Models\Meeting;

class Show extends Component
{
    public $meeting;

    public function mount(Meeting $meeting)
    {
        $this->meeting = $meeting->load([
            'team',
            'status',
            'responsibles'
        ]);
    }

    public function render()
    {
        return view('livewire.modules.meetings.show');
    }
}
