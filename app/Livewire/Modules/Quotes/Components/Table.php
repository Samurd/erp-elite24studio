<?php

namespace App\Livewire\Modules\Quotes\Components;

use App\Models\Quote;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    protected $listeners = ["refresh" => '$refresh'];

    public function render()
    {
        $quotes = Quote::paginate(10);

        return view("livewire.modules.quotes.components.table", [
            "quotes" => $quotes,
        ]);
    }
}
