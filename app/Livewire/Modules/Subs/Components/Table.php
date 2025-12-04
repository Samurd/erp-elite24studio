<?php

namespace App\Livewire\Modules\Subs\Components;

use App\Models\Sub;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{

    use WithPagination;

    public function mount()
    {
    }
    public function render()
    {
        $subs = Sub::paginate(10);


        return view('livewire.modules.subs.components.table', [
            'subs'=> $subs
        ]);
    }
}
