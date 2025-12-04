<?php

namespace App\Livewire\Modules\Reports\Components;

use App\Models\Report;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{


    use WithPagination;
    
    protected $listeners = ['refresh'=> '$refresh'];


    public function mount()
    {

    }
    public function render()
    {

        $reports = Report::paginate(10);

        return view('livewire.modules.reports.components.table', [
            'reports'=> $reports
        ]);
    }
}
