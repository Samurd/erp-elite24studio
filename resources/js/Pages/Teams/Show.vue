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
    channels: Array, // All channels with is_channel_member flag
    members: Array,
    isMember: Boolean,
    currentUserRole: Object,
    teamRoles: Array,
    availableUsers: Array,
    isPrivateTeamNonMember: Boolean,
    isMember: Boolean,
    currentUserRole: Object,
    teamRoles: Array,
    availableUsers: Array,
    isPrivateTeamNonMember: Boolean,
});

const activeChannel = ref(null);
const activeTab = ref('canales');

// Check URL params for initial channel on mount (optional but good for UX)
// const urlParams = new URLSearchParams(window.location.search);
// const channelId = urlParams.get('channel');
// if (channelId) {
//    activeChannel.value = props.channels.find(c => c.id == channelId);
//    if (activeChannel.value) activeTab.value = 'chat';
// }

const selectChannel = (ch) => {
    activeChannel.value = ch;
    activeTab.value = 'chat';
};

const deselectChannel = () => {
    activeChannel.value = null;
    activeTab.value = 'canales';
};

// Initialize active channel if none selected? Or maybe select First?
// Let's keep it null to show dashboard/tabs first.
const showAddMemberModal = ref(false);
const showChannelModal = ref(false);
const showAddMember = ref(false); // For inline toggle in Members tab

// Flash messages handling
const page = usePage();
const flashMessage = computed(() => page.props.flash.message);
const flashError = computed(() => page.props.flash.error);

const searchMemberQuery = ref('');

// Compute derived data for members to match blade logic
const filteredMembers = computed(() => {
    if (!searchMemberQuery.value) return props.members;
    const q = searchMemberQuery.value.toLowerCase();
    return props.members.filter(m => 
        m.name.toLowerCase().includes(q) || 
        m.email.toLowerCase().includes(q)
    );
});

const owners = computed(() => filteredMembers.value.filter(m => m.role_slug === 'owner'));
const regularMembers = computed(() => filteredMembers.value.filter(m => m.role_slug === 'member'));

const organizedChannels = computed(() => {
    // Ensure channels prop exists
    if (!props.channels) return [];
    
    // Sort logic could go here
    const parents = props.channels.filter(c => !c.parent_id);
    return parents.map(p => ({
        ...p,
        children: props.channels.filter(c => c.parent_id === p.id)
    }));
});

const channelForm = useForm({
    id: null,
    name: '',
    description: '',
    is_private: false,
    parent_id: null,
});

// ... (keep existing imports and props)

const openCreateChannelModal = (parentId = null) => {
    channelForm.reset();
    channelForm.id = null;
    channelForm.parent_id = parentId;
    showChannelModal.value = true;
};

const openEditChannelModal = (ch) => {
    channelForm.id = ch.id;
    channelForm.name = ch.name;
    channelForm.description = ch.description;
    channelForm.is_private = !!ch.is_private;
    channelForm.parent_id = ch.parent_id;
    showChannelModal.value = true;
};

// ... (keep existing methods)



const memberForm = useForm({
    user_id: '',
});

const teamForm = useForm({
    _method: 'PUT',
    name: props.team.name,
    description: props.team.description,
    isPublic: !!props.team.isPublic,
    photo: null,
});


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
    teamForm.post(route('teams.update', props.team.id), {
        forceFormData: true,
    });
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

const toggleAddMember = () => {
    showAddMember.value = !showAddMember.value;
    if (showAddMember.value) {
        router.reload({ only: ['availableUsers'] });
    }
};

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
                <!-- Contenido principal -->
                <div class="flex-1 overflow-hidden relative bg-white">
                    <template v-if="activeChannel">
                         <!-- Channel Chat wrapper -->
                         <div class="h-full flex flex-col">
                             <template v-if="!isMember">
                                 <div class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 z-10">
                                     <!-- Not a team member lock screen -->
                                     <div class="absolute top-4 left-4 z-20">
                                        <button @click="deselectChannel" class="text-gray-500 hover:text-gray-700 hover:bg-gray-100 p-2 rounded-full transition-colors">
                                            <i class="fas fa-arrow-left"></i>
                                        </button>
                                     </div>
                                     <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mb-4">
                                         <i class="fas fa-lock text-gray-400 text-3xl"></i>
                                     </div>
                                     <h3 class="text-xl font-semibold text-gray-900">Únete al equipo</h3>
                                     <button @click="joinTeam" class="mt-4 bg-green-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-green-700 transition">
                                         Unirse al Equipo
                                     </button>
                                 </div>
                             </template>
                             <template v-else-if="!activeChannel.is_channel_member">
                                  <div class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 z-10">
                                     <!-- Not a channel member lock screen -->
                                     <div class="absolute top-4 left-4 z-20">
                                        <button @click="deselectChannel" class="text-gray-500 hover:text-gray-700 hover:bg-gray-100 p-2 rounded-full transition-colors">
                                            <i class="fas fa-arrow-left"></i>
                                        </button>
                                     </div>
                                     <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                                         <i class="fas fa-hashtag text-blue-500 text-3xl"></i>
                                     </div>
                                     <h3 class="text-xl font-semibold text-gray-900">Canal #{{ activeChannel.name }}</h3>
                                     <p class="text-gray-500 mt-2 mb-6 max-w-sm text-center">Únete a este canal para participar.</p>
                                     <div v-if="activeChannel.is_private" class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-lg text-sm">
                                         <i class="fas fa-lock mr-2"></i> Canal Privado - Requiere invitación
                                     </div>
                                     <button v-else @click="joinChannel(activeChannel.id)" 
                                        class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition">
                                         Unirse al Canal
                                     </button>
                                  </div>
                             </template>
                             <template v-else>
                                 <ChannelChat :team="team" :channel="activeChannel" @back="deselectChannel" />
                             </template>
                         </div>
                    </template>
                    <template v-else>
                        <!-- Tabs View -->
                        <div class="flex flex-col h-full">
                             <!-- Header superior -->
                            <div class="bg-white border-b border-gray-200 px-6 py-4">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="px-2 space-y-2 shrink-0">
                                            <Link :href="route('teams.index')"
                                                class="flex items-center space-x-2 text-gray-500 hover:text-black transition-colors p-2 rounded hover:bg-gray-200">
                                                <i class="fas fa-arrow-left"></i>
                                            </Link>
                                        </div>
                                        <img v-if="team.profile_photo_url" :src="team.profile_photo_url" :alt="team.name" class="w-10 h-10 rounded-md object-cover mr-3" />
                                        <div >
                                        <h2 class="text-2xl font-bold text-gray-900">{{ team.name }}</h2>
                                        <p class="text-gray-600 mt-1">Gestión del equipo</p>
                                        </div>
                                    </div>

                                    <div>
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
                                    
                                    <div v-for="ch in organizedChannels" :key="ch.id" class="mb-4">
                                        <!-- Parent Channel -->
                                        <div class="bg-white rounded-lg border border-gray-200 px-3 py-3 transition-all hover:bg-gray-100">
                                            <div class="flex items-center justify-between">
                                                 <div class="flex items-start space-x-4 flex-1">
                                                     <div class="flex-1 min-w-0">
                                                         <div class="flex items-center space-x-3">
                                                             <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                                                 <i class="fas fa-hashtag text-gray-400 mr-1 text-sm"></i>
                                                                 {{ ch.name }}
                                                             </h3>
                                                             <span v-if="ch.is_private" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                                                 <i class="fas fa-lock mr-1"></i>Privado
                                                             </span>
                                                         </div>
                                                         <p v-if="ch.description" class="text-sm text-gray-500 mt-1 line-clamp-1">{{ ch.description }}</p>
                                                     </div>
                                                     
                                                     <div class="flex items-center space-x-1">
                                                         <template v-if="isMember">
                                                             <template v-if="!ch.is_channel_member">
                                                                 <button v-if="!ch.is_private" @click="joinChannel(ch.id)" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition-colors text-sm font-medium" title="Unirse">
                                                                     Unirse
                                                                 </button>
                                                                 <span v-else class="text-gray-400 text-xs italic p-2"><i class="fas fa-lock"></i></span>
                                                             </template>
                                                        <template v-else>
                                                                <button @click="selectChannel(ch)" class="text-yellow-600 hover:bg-yellow-50 p-2 rounded-lg transition-colors font-medium text-sm" title="Abrir Chat">
                                                                    Abrir
                                                                </button>
                                                                 <button v-if="isOwner" @click="openCreateChannelModal(ch.id)" class="text-green-600 hover:bg-green-50 p-2 rounded-lg transition-colors" title="Crear Subcanal">
                                                                     <i class="fas fa-plus"></i>
                                                                 </button>
                                                                  <button v-if="isOwner" @click="openEditChannelModal(ch)" class="text-blue-600 hover:bg-blue-50 p-2 rounded-lg transition-colors" title="Editar">
                                                                     <i class="fas fa-pen"></i>
                                                                 </button>
                                                                 <button v-if="ch.is_private || isOwner" @click="isOwner ? deleteChannel(ch) : leaveChannel(ch.id)" 
                                                                    class="text-red-500 hover:bg-red-50 p-2 rounded-lg transition-colors" :title="isOwner ? 'Eliminar' : 'Salir'">
                                                                     <i class="fas" :class="isOwner ? 'fa-trash' : 'fa-sign-out-alt'"></i>
                                                                 </button>
                                                             </template>
                                                         </template>
                                                     </div>
                                                 </div>
                                            </div>
                                        </div>

                                        <!-- Subchannels -->
                                        <div v-if="ch.children && ch.children.length > 0" class="ml-4 pl-6 border-l-4 border-gray-200 mt-3 space-y-3" style="margin-left: 1rem;">
                                            <div v-for="sub in ch.children" :key="sub.id" class="bg-white rounded-lg border border-gray-200 px-4 py-3 hover:bg-gray-100 transition-all group relative">
                                                <!-- Connector Line (Visual enhancement) -->
                                                <!-- <div class="absolute -left-10 top-1/2 w-8 h-px bg-gray-300"></div> -->

                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-3 flex-1">
                                                        <div class="flex flex-col items-center">
                                                            <i class="fas fa-level-up-alt rotate-90 text-blue-400 text-sm"></i>
                                                        </div>
                                                        <div class="min-w-0">
                                                            <div class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800 mb-1">
                                                                SUBCANAL DE {{ ch.name }}
                                                            </div>
                                                            <div class="flex items-center space-x-2">
                                                                <h4 class="text-base font-medium text-gray-900">{{ sub.name }}</h4>
                                                                <i v-if="sub.is_private" class="fas fa-lock text-xs text-gray-400"></i>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center space-x-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                                        <template v-if="!sub.is_channel_member">
                                                             <button v-if="!sub.is_private" @click="joinChannel(sub.id)" class="text-xs text-blue-600 hover:underline px-2">Unirse</button>
                                                        </template>
                                                        <template v-else>
                                                            <button @click="selectChannel(sub)" class="text-gray-500 hover:text-yellow-600 p-1.5"><i class="fas fa-comment-alt text-xs"></i></button>
                                                            <button v-if="isOwner" @click="openEditChannelModal(sub)" class="text-gray-500 hover:text-blue-600 p-1.5"><i class="fas fa-pen text-xs"></i></button>
                                                            <button v-if="sub.is_private || isOwner" @click="isOwner ? deleteChannel(sub) : leaveChannel(sub.id)" class="text-gray-500 hover:text-red-600 p-1.5">
                                                                <i class="fas text-xs" :class="isOwner ? 'fa-trash' : 'fa-sign-out-alt'"></i>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div v-if="channels.length === 0" class="text-center py-12">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-hashtag text-gray-400 text-2xl"></i>
                                        </div>
                                        <h3 class="text-md font-medium text-gray-900">Sin canales</h3>
                                        <p class="text-gray-500 text-sm mt-1">Crea canales para organizar las conversaciones.</p>
                                    </div>
                                </div>

                                <!-- Miembros -->
                                <div v-if="activeTab === 'miembros'">
                                     <div class="flex items-center justify-between mb-6">
                                        <h3 class="text-lg font-semibold text-gray-900">Miembros del Equipo</h3>
                                        
                                        <div class="flex items-center gap-3">
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <i class="fas fa-search text-gray-400"></i>
                                                </div>
                                                <input 
                                                    v-model="searchMemberQuery" 
                                                    type="text" 
                                                    placeholder="Buscar miembro..." 
                                                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-yellow-500 focus:border-yellow-500 w-64 transition-all"
                                                >
                                            </div>

                                            <button v-if="isOwner" @click="toggleAddMember"
                                                class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center text-sm font-medium">
                                                <i class="fas fa-user-plus mr-2"></i> Agregar
                                            </button>
                                        </div>
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
                                                        <img v-if="member.profile_photo_url" :src="member.profile_photo_url" :alt="member.name" class="w-12 h-12 rounded-full object-cover" />
                                                        <div v-else class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-full flex items-center justify-center text-white font-medium text-lg">
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
                                                        <img v-if="member.profile_photo_url" :src="member.profile_photo_url" :alt="member.name" class="w-12 h-12 rounded-full object-cover" />
                                                        <div v-else class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-medium text-lg">
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
                                                     <InputLabel value="Foto de Perfil" />
                                                     <input type="file" @input="teamForm.photo = $event.target.files[0]" class="w-full" :disabled="!isOwner" />
                                                     <InputError :message="teamForm.errors.photo" class="mt-2" />
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
                          <InputLabel value="Canal Padre (Opcional)" />
                          <select v-model="channelForm.parent_id" class="w-full mt-1 border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm">
                              <option :value="null">Ninguno (Canal Principal)</option>
                              <option v-for="ch in channels.filter(c => !c.parent_id && c.id !== channelForm.id)" :key="ch.id" :value="ch.id">
                                  {{ ch.name }}
                              </option>
                          </select>
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
