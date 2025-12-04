<?php

namespace App\Livewire\Modules\Policies;

use Livewire\Component;
use App\Models\Policy;

class Show extends Component
{
    public $policy;

    public function mount(Policy $policy)
    {
        $this->policy = $policy->load([
            'type',
            'status',
            'assignedTo',
            'assignedTo',
            'files.folder',
            'files.user'
        ]);
    }

    public function render()
    {
        return view('livewire.modules.policies.show');
    }
}