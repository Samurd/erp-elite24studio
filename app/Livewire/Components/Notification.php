<?php

namespace App\Livewire\Components;

use Livewire\Component;

class Notification extends Component
{
    public $show = false;
    public $type = 'info'; // success, error, warning, info
    public $message = '';
    public $duration = 3000; // 3 segundos por defecto

    protected $listeners = [
        'show-notification' => 'showNotification',
        'hide-notification' => 'hideNotification'
    ];

    public function showNotification($data)
    {
        $this->type = $data['type'] ?? 'info';
        $this->message = $data['message'] ?? '';
        $this->duration = $data['duration'] ?? 3000;
        $this->show = true;

        // Auto ocultar después de la duración
        if ($this->duration > 0) {
            $this->dispatch('auto-hide-notification', ['duration' => $this->duration]);
        }
    }

    public function hideNotification()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.components.notification');
    }
}
