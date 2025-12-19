<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import NotificationDropdown from '@/Components/NotificationDropdown.vue';
import { BellPlus } from 'lucide-vue-next';

const page = usePage();
const permissions = computed(() => page.props.auth.permissions);
const user = computed(() => page.props.auth.user);
const userRole = computed(() => page.props.auth.user_role);
const profilePhotoUrl = computed(() => page.props.auth.profile_photo_url);

const showingNavigationDropdown = ref(false);
const showingProfileDropdown = ref(false);

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <header class="h-16 bg-white p-4 flex items-center justify-between z-20 shadow-sm fixed top-0 left-[280px] right-0">
        <!-- Search Bar -->
        <div class="w-2/3 relative ml-5">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fas fa-search text-gray-400 text-xl"></i>
            </span>
            <input type="text" placeholder="Buscar aquí" class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-yellow-500" />
        </div>

        <!-- Icons and Avatar -->
        <div class="flex items-center space-x-6 relative mr-5">

            <!-- Modulo para crear notificaciones, recodatorios, etc. -->
            <Link :href="route('notifications.index')" class="cursor-pointer text-gray-500 hover:text-gray-700">
                 <BellPlus color="black" size="25" stroke="4" />
            </Link>

            <!-- Chats privados -->
            <Link :href="route('teams.chats')" class="cursor-pointer text-gray-500 hover:text-gray-700">
                <!-- <x-ri-chat-private-line class="w-8 h-8" /> -->
                <i class="fas fa-comments text-2xl text-black"></i>
            </Link>

            <!-- Modulo Teams -->
            <a href="/teams" class="cursor-pointer text-gray-500 hover:text-gray-700">
                <!-- <x-bi-microsoft-teams class="w-6 h-6" /> -->
                <i class="fab fa-microsoft text-2xl text-black"></i>
            </a>

            <!-- Usuarios -->
            <Link v-if="permissions.usuarios" href="/users" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-gray-700">
                 <!-- <x-clarity-users-solid /> -->
                 <i class="fas fa-users text-2xl text-black"></i>
            </Link>

            <!-- Notificaciones -->
            <NotificationDropdown />

            <!-- User Information -->
            <div class="flex flex-col justify-center text-right hidden md:flex">
                <span class="text-sm font-medium text-gray-700">{{ user.name }}</span>
                <div class="bg-gradient-to-r from-black via-yellow-600 to-yellow-400 w-full flex items-center justify-center rounded-lg text-white text-xs font-semibold px-2 py-0.5">
                    <span>{{ userRole }}</span>
                </div>
            </div>

            <!-- Perfil Dropdown -->
            <div class="relative">
                <div @click="showingProfileDropdown = !showingProfileDropdown" class="cursor-pointer">
                    <img 
                        :src="profilePhotoUrl || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&color=7F9CF5&background=EBF4FF`" 
                        :alt="user.name" 
                        class="w-10 h-10 rounded-full object-cover border-2 border-transparent hover:border-gray-300 transition"
                        @error="$event.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&color=7F9CF5&background=EBF4FF`"
                    />
                </div>

                <!-- Dropdown Content -->
                <div v-show="showingProfileDropdown" @click.away="showingProfileDropdown = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5">
                    
                    <div class="block px-4 py-2 text-xs text-gray-400">
                        Administrar Cuenta
                    </div>

                        <Link :href="route('profile.show')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                        <i class="fas fa-user-circle mr-3 text-gray-500"></i> Perfil
                    </Link>

                    <div class="border-t border-gray-100"></div>

                    <form @submit.prevent="logout">
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                            <i class="fas fa-sign-out-alt mr-3 text-gray-500"></i> Cerrar sesión
                        </button>
                    </form>
                </div>
                <!-- Backdrop for dropdown -->
                <div v-if="showingProfileDropdown" @click="showingProfileDropdown = false" class="fixed inset-0 z-40"></div>
            </div>

        </div>
    </header>
</template>
