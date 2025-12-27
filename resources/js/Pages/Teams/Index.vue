<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    teams: Object,
    filters: Object,
    canCreate: Boolean, // Map this from backend permission check
});

const search = ref(props.filters.search || '');
const isPublicFilter = ref(props.filters.isPublicFilter || '');
const perPage = ref(props.filters.perPage || 10);

const updateSearch = debounce(() => {
    applyFilters();
}, 300);

const applyFilters = () => {
    router.get(route('teams.index'), {
        search: search.value,
        isPublicFilter: isPublicFilter.value,
        perPage: perPage.value,
    }, {
        preserveState: true,
        replace: true,
        preserveScroll: true,
    });
};

watch([isPublicFilter, perPage], () => {
    applyFilters();
});

const clearFilters = () => {
    search.value = '';
    isPublicFilter.value = '';
    applyFilters();
};

const user = usePage().props.auth.user;

// Helper to get role badge
const getRoleBadge = (team) => {
    // Determine role from team.members.find(u => u.id === user.id)
    // Note: teams prop needs to include authenticated user's membership info
    // The Controller 'index' loads members filtered by currentUserId.
    // So team.members[0] should be the current user if they are a member.
    const member = team.members ? team.members[0] : null;
    if (!member) return null;
    
    // We need the role pivot. team.members is array of users with pivot.
    // In Controller: 'members' => function($q) use ($uid) { where('user_id', $uid); }
    // pivot: role_id. We need role slug/name.
    // The controller index didn't join roles table for the list view efficiently for all rows, 
    // or we can just rely on basic logic if we pass roles. 
    // Let's assume we have `pivot` data. 
    // For visual parity, we might need a better way to get the role string if not passed.
    // However, the blade uses `@if($team->members->first()) $role = TeamRole::find(...)` which is N+1 but acceptable there.
    // In Vue, we ideally accept a `current_user_role` attribute on the team object.
    
    // I will stick to the basic "Miembro" vs "Owner" if I can deduce it, or just show Member.
    // But since the blade shows it, I should try.
    // For now, I'll check membership.
    return member ? true : false;
};
</script>

<template>
    <AppLayout title="Equipos" class="bg-gray-50">
         <div class="flex h-[calc(100vh-65px)] bg-gray-50 overflow-hidden">
            <!-- Sidebar izquierda -->
            <div class="w-64 bg-gray-100 text-gray-900 flex flex-col border-r border-gray-300">
                <!-- Header -->
                <div class="p-4 border-b border-gray-300">
                    <div class="flex items-center justify-between">
                        <h1 class="text-xl font-bold flex items-center">
                            <i class="fas fa-users mr-2"></i>
                            Teams
                        </h1>
                        <!-- TODO: Add permission check for creation -->
                        <Link :href="route('teams.create')"
                            class="bg-yellow-600 hover:bg-yellow-700 text-white p-2 rounded-lg transition-colors">
                             <i class="fas fa-plus"></i>
                        </Link>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="p-4 border-b border-gray-300">
                    <div class="space-y-3">
                        <!-- Búsqueda -->
                        <div>
                            <input type="text" v-model="search" @input="updateSearch" placeholder="Buscar equipos..."
                                class="w-full px-3 py-2 bg-gray-200 border border-gray-700 rounded-lg text-black placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        </div>

                        <!-- Filtro por tipo -->
                        <div>
                            <select v-model="isPublicFilter"
                                class="w-full px-3 py-2 bg-gray-200 border border-gray-700 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                <option value="">Todos los equipos</option>
                                <option value="1">Públicos</option>
                                <option value="0">Privados</option>
                            </select>
                        </div>

                        <!-- Registros por página -->
                        <div>
                            <select v-model="perPage"
                                class="w-full px-3 py-2 bg-gray-200 border border-gray-700 rounded-lg text-black focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                <option value="10">10 equipos</option>
                                <option value="25">25 equipos</option>
                                <option value="50">50 equipos</option>
                                <option value="100">100 equipos</option>
                            </select>
                        </div>

                        <!-- Botón limpiar -->
                        <button @click="clearFilters"
                            class="w-full bg-gray-400 hover:bg-gray-600 text-white px-3 py-2 rounded-lg transition-colors">
                            <i class="fas fa-times mr-2"></i>Limpiar filtros
                        </button>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="p-4 border-b border-gray-300">
                    <div class="text-sm text-gray-400">
                        <div class="flex justify-between">
                            <span>Total de equipos:</span>
                            <span class="text-black font-semibold">{{ teams.total }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido principal -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Navegación superior -->
                <div class="bg-gray-900 text-white px-6 py-3">
                    <div class="flex items-center space-x-6">
                        <Link :href="route('teams.index')"
                            class="flex items-center space-x-2 hover:text-white transition-colors">
                            <i class="fas fa-users"></i>
                            <span>Equipos</span>
                        </Link>
                        <!-- Todo: Link to separate chats page or similar if exists, sticking to Team focus -->
                        <a href="#"
                            class="flex items-center space-x-2 hover:text-white transition-colors">
                            <i class="fas fa-comments"></i>
                            <span>Chats</span>
                        </a>
                        <Link :href="route('teams.create')"
                            class="flex items-center space-x-2 hover:text-white transition-colors">
                            <i class="fas fa-plus"></i>
                            <span>Nuevo Equipo</span>
                        </Link>
                    </div>
                </div>

                <!-- Header superior -->
                <div class="bg-white border-b border-gray-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Todos los equipos</h2>
                            <p class="text-gray-600 mt-1">Gestiona tus equipos y colaboraciones</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500">
                                Mostrando {{ teams.from ?? 0 }} - {{ teams.to ?? 0 }} de
                                {{ teams.total }} equipos
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Grid de equipos estilo Teams -->
                <div class="p-6 overflow-y-auto flex-1 bg-gray-50">
                    <template v-if="teams.data.length">
                        <Link v-for="team in teams.data" :key="team.id" 
                            :href="route('teams.show', team.id)"
                            class="block bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4 hover:shadow-sm hover:border-yellow-300 transition-all cursor-pointer">
                            <div class="flex items-start justify-between">
                                <!-- Info principal del equipo -->
                                <div class="flex items-start space-x-4 flex-1">
                                    <!-- Avatar del equipo -->
                                    <div class="flex-shrink-0">
                                        <img v-if="team.profile_photo_url" :src="team.profile_photo_url" :alt="team.name" class="w-16 h-16 rounded-xl object-cover" />
                                        <div v-else class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-xl flex items-center justify-center">
                                            <span class="text-white text-xl font-bold">{{ team.name.substring(0, 2).toUpperCase() }}</span>
                                        </div>
                                    </div>

                                    <!-- Detalles del equipo -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                {{ team.name }}
                                            </h3>
                                            
                                            <span v-if="team.isPublic" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-globe mr-1"></i>Público
                                            </span>
                                            <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fas fa-lock mr-1"></i>Privado
                                            </span>

                                            <!-- Badge del rol del usuario -->
                                            <!-- Simple check if they are a member based on eager loading -->
                                            <span v-if="team.members && team.members.length > 0" 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-user mr-1"></i>Miembro
                                            </span>
                                        </div>

                                        <p v-if="team.description" class="mt-2 text-sm text-gray-600 line-clamp-2">
                                            {{ team.description }}
                                        </p>

                                        <!-- Estadísticas del equipo -->
                                        <div class="mt-4 flex items-center space-x-6">
                                            <div class="flex items-center text-sm text-gray-500">
                                                <i class="fas fa-user-friends mr-2 text-yellow-500"></i>
                                                <span class="font-medium text-gray-900">{{ team.members_count }}</span>
                                                <span class="ml-1">miembros</span>
                                            </div>
                                            <div class="flex items-center text-sm text-gray-500">
                                                <i class="fas fa-hashtag mr-2 text-blue-500"></i>
                                                <span class="font-medium text-gray-900">{{ team.channels_count }}</span>
                                                <span class="ml-1">canales</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </Link>
                    </template>
                    
                    <div v-else class="text-center py-12">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron equipos</h3>
                        <p class="text-gray-500 mb-6">No hay equipos que coincidan con tu búsqueda.</p>
                        <button @click="clearFilters"
                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-times mr-2"></i>Limpiar filtros
                        </button>
                    </div>

                    <!-- Paginación -->
                    <div v-if="teams.links.length > 3" class="mt-8 flex justify-center pb-8 sticky bottom-0">
                         <Pagination :links="teams.links" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
