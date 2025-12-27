<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import NotificationDropdown from '@/Components/NotificationDropdown.vue';
import { BellPlus } from 'lucide-vue-next';

const page = usePage();
const permissions = computed(() => page.props.auth.permissions);
const user = computed(() => page.props.auth.user);
const userRole = computed(() => page.props.auth.user_role);
const profilePhotoUrl = computed(() => page.props.auth.user.profile_photo_url);

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
            <Link :href="route('teams.index')" class="cursor-pointer text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-microsoft-teams text-black" viewBox="0 0 16 16">
                    <path d="M9.186 4.797a2.42 2.42 0 1 0-2.86-2.448h1.178c.929 0 1.682.753 1.682 1.682zm-4.295 7.738h2.613c.929 0 1.682-.753 1.682-1.682V5.58h2.783a.7.7 0 0 1 .682.716v4.294a4.197 4.197 0 0 1-4.093 4.293c-1.618-.04-3-.99-3.667-2.35Zm10.737-9.372a1.674 1.674 0 1 1-3.349 0 1.674 1.674 0 0 1 3.349 0m-2.238 9.488-.12-.002a5.2 5.2 0 0 0 .381-2.07V6.306a1.7 1.7 0 0 0-.15-.725h1.792c.39 0 .707.317.707.707v3.765a2.6 2.6 0 0 1-2.598 2.598z"/>
                    <path d="M.682 3.349h6.822c.377 0 .682.305.682.682v6.822a.68.68 0 0 1-.682.682H.682A.68.68 0 0 1 0 10.853V4.03c0-.377.305-.682.682-.682Zm5.206 2.596v-.72h-3.59v.72h1.357V9.66h.87V5.945z"/>
                </svg>
            </Link>

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
                        v-if="profilePhotoUrl"
                        :src="profilePhotoUrl" 
                        :alt="user.name" 
                        class="w-10 h-10 rounded-full object-cover border-2 border-transparent hover:border-gray-300 transition"
                    />
                    <div 
                        v-else
                        class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold border-2 border-transparent hover:border-gray-300 transition"
                    >
                        {{ user.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() }}
                    </div>
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
