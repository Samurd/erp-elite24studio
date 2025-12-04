<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Notification;
use App\Services\NotificationService;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

class NotificationDropdown extends Component
{
    public $notifications;
    public $unreadCount;
    public $isOpen = false;
    public $userId;

    protected $notificationService;

    public function boot(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function mount()
    {
        $this->userId = Auth::id();
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Notification::where('user_id', Auth::id())
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $this->unreadCount = $this->notificationService->getUnreadCount(Auth::id());
    }

    /**
     * Listener para recargar notificaciones cuando se recibe una nueva
     * Este método es llamado desde JavaScript vía Echo
     */
    public function refreshNotifications()
    {
        $this->loadNotifications();
        $this->dispatch('notification-received');
    }

    public function markAsRead($notificationId)
    {
        $this->notificationService->markAsRead($notificationId, Auth::id());
        $this->loadNotifications();
    }

    public function markAllAsRead()
    {
        $this->notificationService->markAllAsRead(Auth::id());
        $this->loadNotifications();
    }

    public function deleteNotification($notificationId)
    {
        $this->notificationService->deleteNotification($notificationId, Auth::id());
        $this->loadNotifications();
    }

    public function toggleDropdown()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function closeDropdown()
    {
        $this->isOpen = false;
    }

    public function render()
    {
        return view('livewire.components.notification-dropdown');
    }
}
