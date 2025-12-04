<?php

namespace App\Livewire\Modules\Notifications;

use Livewire\Component;
use App\Models\NotificationTemplate;

class Update extends Component
{
    public NotificationTemplate $notification;

    public function mount(NotificationTemplate $notification)
    {
        $this->notification = $notification;
    }

    public function render()
    {
        return view('livewire.modules.notifications.update');
    }
}
