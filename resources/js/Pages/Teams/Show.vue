<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ChannelChat from './Components/ChannelChat.vue';
import DialogModal from '@/Components/DialogModal.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    team: Object,
    channel: Object, // Active channel or null
    channels: Array,
    members: Array,
    isMember: Boolean,
    currentUserRole: Object,
    teamRoles: Array,
    availableUsers: Array,
    isPrivateTeamNonMember: Boolean,
});

const activeTab = ref(props.channel ? 'chat' : 'canales');
const showAddMemberModal = ref(false);
const showChannelModal = ref(false);
const showAddMember = ref(false); // For inline toggle in Members tab

// Flash messages handling
const page = usePage();
const flashMessage = computed(() => page.props.flash.message);
const flashError = computed(() => page.props.flash.error);

// Compute derived data for members to match blade logic
const owners = computed(() => props.members.filter(m => m.role_slug === 'owner'));
const regularMembers = computed(() => props.members.filter(m => m.role_slug === 'member'));

const channelForm = useForm({
    id: null,
    name: '',
    description: '',
    is_private: false,
});

const memberForm = useForm({
    user_id: '',
});

const teamForm = useForm({
    name: props.team.name,
    description: props.team.description,
    isPublic: !!props.team.isPublic
});

// Channel Actions
const openCreateChannelModal = () => {
    channelForm.reset();
    channelForm.id = null;
    showChannelModal.value = true;
};

const openEditChannelModal = (ch) => {
    channelForm.id = ch.id;
    channelForm.name = ch.name;
    channelForm.description = ch.description;
    channelForm.is_private = !!ch.is_private;
    showChannelModal.value = true;
};

const submitChannel = () => {
    if (channelForm.id) {
        channelForm.put(route('teams.channels.update', [props.team.id, channelForm.id]), {
            onSuccess: () => showChannelModal.value = false,
        });
    } else {
        channelForm.post(route('teams.channels.store', props.team.id), {
            onSuccess: () => showChannelModal.value = false,
        });
    }
};

const deleteChannel = (ch) => {
    if (confirm(`¿Estás seguro de eliminar el canal '${ch.name}'? Esta acción no se puede deshacer.`)) {
        router.delete(route('teams.channels.destroy', [props.team.id, ch.id]));
    }
};

const joinChannel = (chId) => {
    router.post(route('teams.channels.join', [props.team.id, chId]));
};

const leaveChannel = (chId) => {
    if (confirm('¿Abandonar canal?')) {
        router.post(route('teams.channels.leave', [props.team.id, chId]));
    }
};

// Team Actions
const updateTeam = () => {
    teamForm.put(route('teams.update', props.team.id));
};

const joinTeam = () => {
    router.post(route('teams.join', props.team.id));
};

const leaveTeam = () => {
    if (confirm('¿Estás seguro de que quieres salir de este equipo? Podrás volver a unirte más tarde si el equipo es público.')) {
        router.post(route('teams.leave', props.team.id));
    }
};

// Member Actions
const addMember = () => {
    memberForm.post(route('teams.members.add', props.team.id), {
        onSuccess: () => {
            showAddMember.value = false;
            memberForm.reset();
        }
    });
};

const removeMember = (userId) => {
    if (confirm('¿Estás seguro de eliminar a este miembro del equipo?')) {
        router.delete(route('teams.members.remove', [props.team.id, userId]));
    }
};

const changeRole = (userId, roleId) => {
    router.put(route('teams.members.role', [props.team.id, userId]), {
        role_id: roleId
    });
};

const confirmDeleteTeam = () => {
    if (confirm('¿ELIMINAR EQUIPO?')) {
        router.delete(route('teams.destroy', props.team.id));
    }
};

const isOwner = computed(() => props.currentUserRole?.slug === 'owner');

</script>

<template>
    <AppLayout :title="team.name" class="bg-gray-50 h-screen overflow-hidden">
        <div class="flex h-[calc(100vh-64px)] bg-gray-50">
            <!-- Notificaciones flotantes -->
            <div v-if="flashMessage" class="fixed top-20 right-4 z-50 bg-green-500 text-white p-4 rounded-lg shadow-lg animate-fade-in">
                {{ flashMessage }}
            </div>
            <div v-if="flashError" class="fixed top-20 right-4 z-50 bg-red-500 text-white p-4 rounded-lg shadow-lg animate-fade-in">
                {{ flashError }}
            </div>

            <template v-if="isPrivateTeamNonMember">
                <div class="flex-1 flex flex-col items-center justify-center bg-gray-50">
                    <div class="text-center max-w-md px-4">
                        <div class="w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-lock text-gray-400 text-4xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">No estás en este equipo</h2>
                        <p class="text-gray-600 mb-8">
                            Parece que aún no te has unido a este equipo. Si deseas participar, puedes solicitar acceso a un administrador.
                        </p>
                        <Link :href="route('teams.index')"
                            class="inline-flex items-center text-yellow-600 hover:text-yellow-700 font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Volver a mis equipos
                        </Link>
                    </div>
                </div>
            </template>
            <template v-else>
                <!-- Sidebar izquierda -->
                <div class="w-64 bg-gray-100 text-gray-900 flex flex-col shrink-0 border-r border-gray-300">
                    <!-- Header -->
                    <div class="p-4 border-b border-gray-300">
                        <div class="flex flex-col space-y-3">
                            <div class="flex items-center justify-between">
                                <h1 class="text-xl font-bold flex items-center">
                                    <i class="fas fa-users mr-2"></i>
                                    {{ team.name }}
                                </h1>
                                <span v-if="isMember && currentUserRole" 
                                    class="px-2 py-1 text-xs font-medium rounded-full border flex items-center"
                                    :class="isOwner ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : 'bg-blue-100 text-blue-800 border-blue-200'">
                                    <i class="fas mr-1 text-xs" :class="isOwner ? 'fa-crown' : 'fa-user'"></i>
                                    {{ currentUserRole.name }}
                                </span>
                            </div>

                            <button v-if="!isMember && team.isPublic" @click="joinTeam"
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center justify-center text-sm font-medium">
                                <i class="fas fa-sign-in-alt mr-2"></i> Unirse al Equipo
                            </button>

                            <button v-if="isMember" @click="leaveTeam"
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center justify-center text-sm font-medium">
                                <i class="fas fa-sign-out-alt mr-2"></i> Salir del Equipo
                            </button>
                        </div>
                    </div>

                    <!-- Información del equipo -->
                    <div class="p-4 border-b border-gray-300">
                        <div class="text-sm text-gray-600 space-y-2">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span>{{ team.isPublic ? 'Equipo Público' : 'Equipo Privado' }}</span>
                            </div>
                            <!-- Date handling omitted for brevity, usually parsed from ISO string -->
                             <div class="flex items-center">
                                <i class="fas fa-calendar mr-2"></i>
                                <span>Creado: {{ new Date(team.created_at).toLocaleDateString() }}</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-user-friends mr-2"></i>
                                <span>{{ members.length }} miembros</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-hashtag mr-2"></i>
                                <span>{{ channels.length }} canales</span>
                            </div>
                        </div>
                    </div>

                    <!-- Miembros del equipo (Sidebar List) -->
                    <div class="p-4 border-b border-gray-300 flex-1 overflow-y-auto max-h-48">
                        <h3 class="text-sm font-semibold mb-3">Miembros del Equipo</h3>
                        <div class="space-y-2">
                            <div v-for="member in members" :key="member.id" class="flex items-center p-2 hover:bg-gray-200 rounded-lg transition-colors group">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3 shrink-0">
                                    <span class="text-yellow-600 font-medium text-sm">
                                        {{ member.name.substring(0, 2) }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-black truncate text-sm">{{ member.name }}</div>
                                    <div class="text-xs text-gray-400 truncate">{{ member.email }}</div>
                                </div>
                                <!-- Chat icon (placeholder href) -->
                                <Link v-if="member.id !== $page.props.auth.user.id" href="#" 
                                    class="opacity-0 group-hover:opacity-100 transition-all ml-2 p-1.5 rounded text-yellow-400 hover:text-yellow-500">
                                    <i class="far fa-comment-dots text-lg"></i>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones rápidas -->
                    <div class="p-4 space-y-2 shrink-0">
                        <Link :href="route('teams.index')"
                            class="flex items-center space-x-2 text-gray-500 hover:text-black transition-colors p-2 rounded hover:bg-gray-200">
                            <i class="fas fa-arrow-left"></i>
                            <span>Volver a equipos</span>
                        </Link>
                    </div>
                </div>

                <!-- Contenido principal -->
                <div class="flex-1 overflow-hidden relative bg-white">
                    <template v-if="channel">
                         <!-- Channel Chat wrapper -->
                         <div class="h-full flex flex-col">
                             <template v-if="!isMember">
                                 <div class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 z-10">
                                     <!-- Not a team member lock screen -->
                                     <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                                         <i class="fas fa-lock text-gray-400 text-3xl"></i>
                                     </div>
                                     <h3 class="text-xl font-semibold text-gray-900">Únete al equipo</h3>
                                     <button @click="joinTeam" class="mt-4 bg-green-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-green-700 transition">
                                         Unirse al Equipo
                                     </button>
                                 </div>
                             </template>
                             <template v-else-if="!channel.is_channel_member">
                                  <div class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 z-10">
                                     <!-- Not a channel member lock screen -->
                                     <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                                         <i class="fas fa-hashtag text-blue-500 text-3xl"></i>
                                     </div>
                                     <h3 class="text-xl font-semibold text-gray-900">Canal #{{ channel.name }}</h3>
                                     <p class="text-gray-500 mt-2 mb-6 max-w-sm text-center">Únete a este canal para participar.</p>
                                     <div v-if="channel.is_private" class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg text-sm">
                                         <i class="fas fa-lock mr-2"></i> Canal Privado - Requiere invitación
                                     </div>
                                     <button v-else @click="joinChannel(channel.id)" 
                                        class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
                                         Unirse al Canal
                                     </button>
                                  </div>
                             </template>
                             <template v-else>
                                 <ChannelChat :team="team" :channel="channel" />
                             </template>
                         </div>
                    </template>
                    <template v-else>
                        <!-- Tabs View -->
                        <div class="flex flex-col h-full">
                             <!-- Header superior -->
                            <div class="bg-white border-b border-gray-200 px-6 py-4">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900">{{ team.name }}</h2>
                                        <p class="text-gray-600 mt-1">Gestión del equipo</p>
                                    </div>
                                </div>
                                <!-- Tabs Navigation -->
                                <div class="flex space-x-1 border-b border-gray-200">
                                    <button @click="activeTab = 'canales'"
                                        class="px-4 py-2 border-b-2 font-medium text-sm transition-colors focus:outline-none"
                                        :class="activeTab === 'canales' ? 'border-yellow-600 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                                        <i class="fas fa-hashtag mr-2"></i> Canales
                                    </button>
                                    <button @click="activeTab = 'miembros'"
                                        class="px-4 py-2 border-b-2 font-medium text-sm transition-colors focus:outline-none"
                                        :class="activeTab === 'miembros' ? 'border-yellow-600 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                                        <i class="fas fa-user-friends mr-2"></i> Miembros
                                    </button>
                                    <button @click="activeTab = 'configuracion'"
                                        class="px-4 py-2 border-b-2 font-medium text-sm transition-colors focus:outline-none"
                                        :class="activeTab === 'configuracion' ? 'border-yellow-600 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'">
                                        <i class="fas fa-cog mr-2"></i> Configuración
                                    </button>
                                </div>
                            </div>

                            <!-- Tab Content -->
                            <div class="flex-1 overflow-y-auto p-6 bg-white">
                                <!-- Canales -->
                                <div v-if="activeTab === 'canales'">
                                     <div class="flex items-center justify-between mb-6">
                                        <h3 class="text-lg font-semibold text-gray-900">Canales de Comunicación</h3>
                                        <button v-if="isOwner" @click="openCreateChannelModal"
                                            class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center">
                                            <i class="fas fa-plus mr-2"></i> Nuevo Canal
                                        </button>
                                    </div>
                                    
                                    <div v-for="ch in channels" :key="ch.id" 
                                        class="bg-white rounded-lg shadow-sm border border-gray-200 px-2 py-2 mb-2 hover:shadow-sm transition-shadow">
                                        <div class="flex items-center justify-between">
                                             <div class="flex items-start space-x-4 flex-1 ml-2">
                                                 <div class="flex-1 min-w-0">
                                                     <div class="flex items-center space-x-3">
                                                         <h3 class="text-lg font-semibold text-gray-900">{{ ch.name }}</h3>
                                                         <span v-if="ch.is_private" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                             <i class="fas fa-lock mr-1"></i>Privado
                                                         </span>
                                                         <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                             <i class="fas fa-globe mr-1"></i>Público
                                                         </span>
                                                          <div class="flex items-center space-x-6 text-sm text-gray-500">
                                                             <div class="flex items-center ml-4">
                                                                <i class="fas fa-user-friends mr-2"></i>
                                                                <span class="font-medium text-gray-900">{{ ch.members_count || 0 }}</span>
                                                                <span class="ml-1">miembros</span>
                                                            </div>
                                                          </div>
                                                     </div>
                                                     <div v-if="ch.description" class="text-sm text-gray-600 mt-1 line-clamp-1">
                                                         {{ ch.description }}
                                                     </div>
                                                 </div>
                                                 
                                                 <!-- Actions -->
                                                 <div class="flex items-center space-x-2 ml-4">
                                                     <template v-if="isMember">
                                                         <template v-if="!ch.is_channel_member">
                                                             <button v-if="!ch.is_private" @click="joinChannel(ch.id)" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition-colors flex items-center text-sm">
                                                                 <i class="fas fa-sign-in-alt mr-2"></i> Unirse
                                                             </button>
                                                             <div v-else class="text-sm text-gray-500 bg-gray-100 px-3 py-2 rounded-lg">
                                                                 <i class="fas fa-lock mr-2"></i> Canal Privado
                                                             </div>
                                                         </template>
                                                         <template v-else>
                                                             <Link :href="route('teams.show', [team.id, ch.id])" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded-lg transition-colors flex items-center text-sm">
                                                                 <i class="fas fa-sign-in-alt mr-2"></i> Entrar
                                                             </Link>
                                                             <button v-if="ch.is_private" @click="leaveChannel(ch.id)" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Abandonar">
                                                                 <i class="fas fa-sign-out-alt"></i>
                                                             </button>
                                                             <button v-if="isOwner" @click="openEditChannelModal(ch)" class="p-2 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors" title="Editar">
                                                                 <i class="fas fa-edit"></i>
                                                             </button>
                                                             <button v-if="isOwner" @click="deleteChannel(ch)" class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
                                                                 <i class="fas fa-trash"></i>
                                                             </button>
                                                         </template>
                                                     </template>
                                                 </div>
                                             </div>
                                        </div>
                                    </div>
                                    <div v-if="channels.length === 0" class="text-center py-12">
                                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-hashtag text-gray-400 text-3xl"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay canales</h3>
                                        <p class="text-gray-500 mb-6">Este equipo aún no tiene canales.</p>
                                    </div>
                                </div>

                                <!-- Miembros -->
                                <div v-if="activeTab === 'miembros'">
                                     <div class="flex items-center justify-between mb-6">
                                        <h3 class="text-lg font-semibold text-gray-900">Miembros del Equipo</h3>
                                        <button v-if="isOwner" @click="showAddMember = !showAddMember"
                                            class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center">
                                            <i class="fas fa-user-plus mr-2"></i> Agregar Miembro
                                        </button>
                                    </div>
                                    
                                    <div v-show="showAddMember" class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                                        <h4 class="text-md font-semibold text-gray-900 mb-4">Agregar Nuevo Miembro</h4>
                                        <div class="space-y-4">
                                            <div>
                                                <InputLabel value="Seleccionar Usuario" />
                                                <select v-model="memberForm.user_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                                    <option value="">Seleccione...</option>
                                                     <option v-for="u in availableUsers" :key="u.id" :value="u.id">{{ u.name }} ({{ u.email }})</option>
                                                </select>
                                            </div>
                                            <div class="flex space-x-3">
                                                 <PrimaryButton @click="addMember" :disabled="memberForm.processing">Agregar</PrimaryButton>
                                                 <SecondaryButton @click="showAddMember = false">Cancelar</SecondaryButton>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Owners -->
                                    <div v-if="owners.length > 0" class="mb-6">
                                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                            <i class="fas fa-crown text-yellow-500 mr-2"></i> Propietarios ({{ owners.length }})
                                        </h4>
                                        <div class="space-y-3">
                                            <div v-for="member in owners" :key="member.id" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-4">
                                                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-full flex items-center justify-center text-white font-medium text-lg">
                                                            {{ member.name.substring(0, 2) }}
                                                        </div>
                                                        <div>
                                                            <div class="flex items-center gap-2">
                                                                <h4 class="font-semibold text-gray-900">{{ member.name }}</h4>
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                    <i class="fas fa-crown mr-1"></i> Propietario
                                                                </span>
                                                            </div>
                                                            <p class="text-sm text-gray-600">{{ member.email }}</p>
                                                        </div>
                                                    </div>
                                                    <!-- Actions (simplified) -->
                                                    <div v-if="isOwner && member.id !== $page.props.auth.user.id" class="flex items-center">
                                                        <button @click="changeRole(member.id, teamRoles.find(r => r.slug === 'member').id)" class="text-gray-600 p-2 hover:bg-gray-100 rounded" title="Hacer Miembro"><i class="fas fa-user-friends"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                     <!-- Members -->
                                    <div v-if="regularMembers.length > 0">
                                        <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                            <i class="fas fa-users text-blue-500 mr-2"></i> Miembros ({{ regularMembers.length }})
                                        </h4>
                                        <div class="space-y-3">
                                            <div v-for="member in regularMembers" :key="member.id" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-4">
                                                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-medium text-lg">
                                                            {{ member.name.substring(0, 2) }}
                                                        </div>
                                                        <div>
                                                            <div class="flex items-center gap-2">
                                                                <h4 class="font-semibold text-gray-900">{{ member.name }}</h4>
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                                    <i class="fas fa-user mr-1"></i> Miembro
                                                                </span>
                                                            </div>
                                                            <p class="text-sm text-gray-600">{{ member.email }}</p>
                                                        </div>
                                                    </div>
                                                    <!-- Actions -->
                                                    <div v-if="isOwner" class="flex items-center">
                                                        <button @click="changeRole(member.id, teamRoles.find(r => r.slug === 'owner').id)" class="text-yellow-600 p-2 hover:bg-yellow-50 rounded" title="Hacer Propietario"><i class="fas fa-crown"></i></button>
                                                        <button @click="removeMember(member.id)" class="text-red-600 p-2 hover:bg-red-50 rounded" title="Eliminar"><i class="fas fa-times"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Configuracion -->
                                <div v-if="activeTab === 'configuracion'" class="max-w-4xl">
                                     <h3 class="text-lg font-semibold text-gray-900 mb-6">Configuración del Equipo</h3>
                                     <form @submit.prevent="updateTeam" class="space-y-6">
                                         <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                             <h4 class="font-semibold text-gray-900 mb-4">Información Básica</h4>
                                             <div class="space-y-4">
                                                 <div>
                                                     <InputLabel value="Nombre del Equipo" />
                                                     <TextInput v-model="teamForm.name" class="w-full" :disabled="!isOwner" :class="{'bg-gray-100': !isOwner}" />
                                                 </div>
                                                 <div>
                                                     <InputLabel value="Descripción" />
                                                     <textarea v-model="teamForm.description" rows="3" class="w-full border-gray-300 rounded-lg focus:ring-yellow-500" :disabled="!isOwner" :class="{'bg-gray-100': !isOwner}"></textarea>
                                                 </div>
                                             </div>
                                         </div>
                                         
                                         <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                                             <h4 class="font-semibold text-gray-900 mb-4">Privacidad</h4>
                                             <div class="grid grid-cols-2 gap-4">
                                                 <label class="relative cursor-pointer">
                                                     <input type="radio" v-model="teamForm.isPublic" :value="true" class="peer sr-only" :disabled="!isOwner">
                                                     <div class="p-4 border-2 rounded-lg peer-checked:border-yellow-600 peer-checked:bg-yellow-50 hover:bg-gray-50 transition-all flex flex-col items-center text-center">
                                                         <i class="fas fa-globe text-2xl text-green-600 mb-2"></i>
                                                         <span class="font-semibold">Público</span>
                                                         <span class="text-xs text-gray-500">Visible para todos</span>
                                                     </div>
                                                 </label>
                                                 <label class="relative cursor-pointer">
                                                     <input type="radio" v-model="teamForm.isPublic" :value="false" class="peer sr-only" :disabled="!isOwner">
                                                     <div class="p-4 border-2 rounded-lg peer-checked:border-yellow-600 peer-checked:bg-yellow-50 hover:bg-gray-50 transition-all flex flex-col items-center text-center">
                                                         <i class="fas fa-lock text-2xl text-gray-600 mb-2"></i>
                                                         <span class="font-semibold">Privado</span>
                                                         <span class="text-xs text-gray-500">Solo miembros</span>
                                                     </div>
                                                 </label>
                                             </div>
                                         </div>
                                         
                                         <div v-if="isOwner" class="flex justify-end">
                                             <PrimaryButton :disabled="teamForm.processing" class="bg-yellow-600 hover:bg-yellow-700">Guardar Cambios</PrimaryButton>
                                         </div>
                                     </form>
                                     
                                     <div v-if="isOwner" class="mt-8 pt-6 border-t border-gray-200">
                                         <h4 class="text-red-600 font-bold mb-4">Zona de Peligro</h4>
                                         <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center justify-between">
                                             <div>
                                                 <h5 class="text-red-800 font-semibold">Eliminar Equipo</h5>
                                                 <p class="text-red-600 text-sm">Esta acción es irreversible.</p>
                                             </div>
                                             <button @click="confirmDeleteTeam" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">Eliminar</button>
                                         </div>
                                     </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        <!-- Modals -->
        <DialogModal :show="showChannelModal" @close="showChannelModal = false">
            <template #title>{{ channelForm.id ? 'Editar Canal' : 'Nuevo Canal' }}</template>
            <template #content>
                 <div class="space-y-4">
                     <div>
                         <InputLabel value="Nombre" />
                         <TextInput v-model="channelForm.name" class="w-full mt-1" />
                         <InputError :message="channelForm.errors.name" />
                     </div>
                     <div>
                         <InputLabel value="Descripción" />
                         <TextInput v-model="channelForm.description" class="w-full mt-1" />
                     </div>
                     <div>
                         <label class="flex items-center">
                             <Checkbox v-model:checked="channelForm.is_private" />
                             <span class="ml-2 text-sm text-gray-600">Canal Privado</span>
                         </label>
                     </div>
                 </div>
            </template>
            <template #footer>
                <SecondaryButton @click="showChannelModal = false" class="mr-2">Cancelar</SecondaryButton>
                <PrimaryButton @click="submitChannel" :disabled="channelForm.processing">Guardar</PrimaryButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>
