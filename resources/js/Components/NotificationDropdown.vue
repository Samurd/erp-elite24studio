<script setup>
import { ref, onMounted, computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import axios from 'axios';
import moment from 'moment'; // Ensure moment is available or use a lightweight alternative

const page = usePage();
const userId = computed(() => page.props.auth.user.id);

const isOpen = ref(false);
const notifications = ref([]);
const unreadCount = ref(0);
const loading = ref(false);

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const closeDropdown = () => {
    isOpen.value = false;
};

const fetchNotifications = async () => {
    loading.value = true;
    try {
        const response = await axios.get(route('notifications.api.index'));
        notifications.value = response.data.notifications;
        unreadCount.value = response.data.unreadCount;
    } catch (error) {
        console.error('Error fetching notifications:', error);
    } finally {
        loading.value = false;
    }
};

const markAsRead = async (notification) => {
    try {
        await axios.post(route('notifications.api.read', { id: notification.id }));
        // Optimistic update
        notification.read_at = new Date().toISOString();
        unreadCount.value = Math.max(0, unreadCount.value - 1);
        // Refresh list to be sure or just filter/update local state? 
        // Let's refetch to keep it simple and accurate for now, or just update local
        fetchNotifications(); 
    } catch (error) {
        console.error('Error marking as read:', error);
    }
};

const markAllAsRead = async () => {
    try {
        await axios.post(route('notifications.api.read-all'));
        notifications.value.forEach(n => n.read_at = new Date().toISOString());
        unreadCount.value = 0;
        fetchNotifications();
    } catch (error) {
        console.error('Error marking all as read:', error);
    }
};

// Helper for Relative Time
const timeAgo = (date) => {
   return moment(date).fromNow();
};

onMounted(() => {
    fetchNotifications();

    // Listen for real-time notifications
    if (window.Echo) {
        window.Echo.private(`notifications.${userId.value}`)
            .listen('.notification.sent', (e) => {
                // console.log('Notification received:', e);
                
                // Show browser notification
                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification(e.title, {
                        body: e.message,
                        icon: '/favicon.ico'
                    });
                }
                
                // Refresh list
                fetchNotifications();
            });
    }
    
    // Request permission if default
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
});
</script>

<template>
    <div class="relative">
        <!-- Trigger -->
        <div @click="toggleDropdown" class="relative w-8 h-8 flex items-center justify-center cursor-pointer text-gray-500 hover:text-gray-700">
            <i class="fas fa-bell text-2xl cursor-pointer text-black"></i>
            
            <!-- Badge -->
            <span v-if="unreadCount > 0" 
                  class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-white text-xs font-bold animate-pulse">
                {{ unreadCount }}
            </span>
        </div>

        <!-- Dropdown Backdrop -->
        <div v-if="isOpen" @click="closeDropdown" class="fixed inset-0 z-40"></div>

        <!-- Dropdown Menu -->
        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 transform scale-95"
            enter-to-class="opacity-100 transform scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 transform scale-100"
            leave-to-class="opacity-0 transform scale-95"
        >
            <div v-show="isOpen" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50 border border-gray-200 overflow-hidden">
                <!-- Header -->
                <div class="px-4 py-3 border-b border-gray-200 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-700">Notificaciones</h3>
                    <button v-if="unreadCount > 0" @click="markAllAsRead" class="text-xs text-blue-600 hover:text-blue-800 transition font-medium">
                        Marcar todas como leídas
                    </button>
                </div>

                <!-- List -->
                <div class="max-h-96 overflow-y-auto">
                    <div v-if="loading && notifications.length === 0" class="p-4 text-center text-gray-500">
                        Cargando...
                    </div>
                    
                    <ul v-else-if="notifications.length > 0">
                        <li v-for="notification in notifications" :key="notification.id" 
                            class="relative p-3 border-b border-gray-100 last:border-none hover:bg-gray-50 transition cursor-pointer group">
                            
                            <!-- Unread Indicator -->
                            <div v-if="!notification.read_at" class="absolute top-4 left-2 w-2 h-2 bg-blue-500 rounded-full"></div>

                            <div class="ml-4">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-800 text-sm">{{ notification.title }}</p>
                                        <p class="text-gray-600 text-sm mt-1">{{ notification.message }}</p>

                                        <!-- Actions URL -->
                                        <Link v-if="notification.data && notification.data.action_url" 
                                              :href="notification.data.action_url" 
                                              class="text-blue-600 text-xs hover:underline mt-1 inline-block">
                                            Ver detalles
                                        </Link>

                                        <p class="text-xs text-gray-400 mt-2 flex items-center">
                                            <i class="far fa-clock mr-1"></i>
                                            {{ timeAgo(notification.created_at) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex space-x-2 mt-2 opacity-0 group-hover:opacity-100 transition">
                                    <button v-if="!notification.read_at" @click.stop="markAsRead(notification)" 
                                            class="text-xs text-blue-600 hover:text-blue-800 transition font-medium">
                                        Marcar como leída
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <div v-else class="p-8 text-center">
                        <div class="text-gray-300 mb-2">
                            <i class="far fa-bell-slash text-4xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm">No tienes notificaciones</p>
                    </div>
                </div>

                <!-- Footer -->
                <div v-if="notifications.length > 0" class="px-4 py-2 border-t border-gray-200 text-center bg-gray-50">
                    <a href="/notifications" class="text-sm text-blue-600 hover:text-blue-800 transition font-medium">
                        Ver todas las notificaciones
                    </a>
                </div>
            </div>
        </transition>
    </div>
</template>
