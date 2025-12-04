<div class="relative" x-data="{ open: @entangle('isOpen') }">
    <!-- Trigger -->
    <div @click="open = !open" class="relative w-8 h-8 flex items-center justify-center cursor-pointer">
        <x-clarity-notification-solid />

        <!-- Badge de contador -->
        @if($unreadCount > 0)
            <span
                class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white text-xs font-bold animate-pulse">
                {{ $unreadCount }}
            </span>
        @endif
    </div>

    <!-- Dropdown -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95" @click.away="open = false"
        class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50 border border-gray-200">
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center">
            <h3 class="font-bold text-gray-700">Notificaciones</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-xs text-blue-600 hover:text-blue-800 transition">
                    Marcar todas como leídas
                </button>
            @endif
        </div>

        <!-- Lista de notificaciones -->
        <div class="max-h-96 overflow-y-auto">
            @if($notifications->count() > 0)
                <ul>
                    @foreach($notifications as $notification)
                        <li
                            class="relative p-3 border-b border-gray-100 last:border-none hover:bg-gray-50 transition cursor-pointer group">
                            <!-- Indicador de no leída -->
                            @if($notification->is_unread)
                                <div class="absolute top-4 left-2 w-2 h-2 bg-blue-500 rounded-full"></div>
                            @endif

                            <!-- Contenido -->
                            <div class="ml-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-800 text-sm">{{ $notification->title }}</p>
                                        <p class="text-gray-600 text-sm mt-1">{{ $notification->message }}</p>

                                        <!-- Datos adicionales si existen -->
                                        @if(!empty($notification->data))
                                            @if(isset($notification->data['action_url']))
                                                <a href="{{ $notification->data['action_url'] }}"
                                                    class="text-blue-600 text-xs hover:underline mt-1 inline-block">
                                                    Ver detalles
                                                </a>
                                            @endif
                                        @endif

                                        <p class="text-xs text-gray-400 mt-2">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Acciones -->
                                <div class="flex space-x-2 mt-2 opacity-0 group-hover:opacity-100 transition">
                                    @if($notification->is_unread)
                                        <button wire:click="markAsRead({{ $notification->id }})"
                                            class="text-xs text-blue-600 hover:text-blue-800 transition">
                                            Marcar como leída
                                        </button>
                                    @endif
                                    <!-- <button wire:click="deleteNotification({{ $notification->id }})"
                                                class="text-xs text-red-600 hover:text-red-800 transition">
                                                Eliminar
                                            </button> -->
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="p-6 text-center">
                    <div class="text-gray-400 mb-2">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm">No tienes notificaciones</p>
                </div>
            @endif
        </div>

        <!-- Footer -->
        @if($notifications->count() > 0)
            <div class="px-4 py-2 border-t border-gray-200 text-center">
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800 transition">
                    Ver todas las notificaciones
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Script para escuchar eventos de Echo -->
<script>
    document.addEventListener('livewire:init', () => {
        // Escuchar nuevas notificaciones en tiempo real
        window.Echo.private(`notifications.{{ $userId }}`)
            .listen('.notification.sent', (e) => {
                // console.log('Nueva notificación recibida:', e);

                // Mostrar notificación del navegador si el usuario la permite
                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification(e.title, {
                        body: e.message,
                        icon: '/favicon.ico'
                    });
                }

                // Actualizar el componente vía Livewire
                @this.call('refreshNotifications');
            });
    });

    // Solicitar permiso para notificaciones del navegador
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
</script>