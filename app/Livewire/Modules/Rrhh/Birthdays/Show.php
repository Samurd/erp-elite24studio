<?php

namespace App\Livewire\Modules\Rrhh\Birthdays;

use Livewire\Component;
use App\Models\Birthday;

class Show extends Component
{
    public Birthday $birthday;

    public function mount(Birthday $birthday)
    {
        $this->birthday = $birthday;
    }

    public function delete()
    {
        $this->birthday->delete();

        session()->flash('success', 'Cumpleaños eliminado exitosamente.');

        return redirect()->route('rrhh.birthdays.index');
    }

    public function render()
    {
        return view('livewire.modules.rrhh.birthdays.show');
    }
}
