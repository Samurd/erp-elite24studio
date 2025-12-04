<?php

namespace App\Livewire\Modules\Rrhh\Attendances;

use Livewire\Component;
use App\Models\Attendance;

class Show extends Component
{
    public Attendance $attendance;

    public function mount(Attendance $attendance)
    {
        $this->attendance = $attendance;
    }

    public function delete()
    {
        $this->attendance->delete();

        session()->flash('success', 'Asistencia eliminada exitosamente.');

        return redirect()->route('rrhh.attendances.index');
    }

    public function render()
    {
        return view('livewire.modules.rrhh.attendances.show');
    }
}
