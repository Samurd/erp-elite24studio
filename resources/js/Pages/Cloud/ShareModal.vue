<script setup>
import { ref, watch, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import axios from 'axios';

const props = defineProps({
    show: Boolean,
    item: Object, // { id, type, name }
});

const emit = defineEmits(['close']);

const tab = ref('users'); // users, teams, link
const loadingData = ref(false);

const users = ref([]);
const teams = ref([]);
const existingShares = ref([]);
const publicLink = ref(null);

// Forms
const shareUserForm = useForm({
    user_id: '',
    permission: 'view',
});

const shareTeamForm = useForm({
    team_id: '',
    permission: 'view',
});

const publicLinkForm = useForm({
    expires_at: '',
});

const fetchData = async () => {
    if (!props.item) return;
    loadingData.value = true;
    try {
        const response = await axios.get(route('cloud.share.data', { type: props.item.type, id: props.item.id }));
        users.value = response.data.users;
        teams.value = response.data.teams;
        existingShares.value = response.data.shares;
        publicLink.value = response.data.publicLink;
    } catch (error) {
        console.error("Error fetching share data", error);
    } finally {
        loadingData.value = false;
    }
};

watch(() => props.show, (newVal) => {
    if (newVal) {
        fetchData();
        // Reset forms
        shareUserForm.reset();
        shareTeamForm.reset();
        publicLinkForm.reset();
        tab.value = 'users';
    }
});

const close = () => {
    emit('close');
};

const shareWithUser = () => {
    shareUserForm.post(route('cloud.share', { type: props.item.type, id: props.item.id }), {
        preserveScroll: true,
        onSuccess: () => {
            shareUserForm.reset();
            fetchData();
        }
    });
};

const shareWithTeam = () => {
    shareTeamForm.post(route('cloud.share', { type: props.item.type, id: props.item.id }), {
        preserveScroll: true,
        onSuccess: () => {
            shareTeamForm.reset();
            fetchData();
        }
    });
};

const removeShare = (shareId) => {
    if (!confirm('¿Quitar acceso?')) return;
    router.delete(route('cloud.unshare', shareId), {
        preserveScroll: true,
        onSuccess: () => fetchData()
    });
};

const generateLink = () => {
    publicLinkForm.post(route('cloud.public-link.create', { type: props.item.type, id: props.item.id }), {
        preserveScroll: true,
        onSuccess: () => fetchData()
    });
};

const deleteLink = () => {
    if (!confirm('¿Eliminar enlace público?')) return;
    router.delete(route('cloud.public-link.delete', { type: props.item.type, id: props.item.id }), {
        preserveScroll: true,
        onSuccess: () => fetchData()
    });
};

const copyLink = () => {
    if (publicLink.value?.url) {
        navigator.clipboard.writeText(publicLink.value.url);
        // Toast or simple alert logic could go here, or simple feedback ref
    }
};

</script>

<template>
    <Modal :show="show" @close="close" maxWidth="md">
        <div class="p-6 bg-[#252525] text-gray-100 rounded-lg">
            <div class="flex justify-between items-center mb-4 border-b border-white/10 pb-2">
                <h3 class="text-lg font-bold">
                    Compartir <span class="text-yellow-500">{{ item?.name }}</span>
                </h3>
                <button @click="close" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Tabs -->
            <div class="flex space-x-4 border-b border-white/10 mb-4">
                <button @click="tab = 'users'" class="pb-2 text-sm font-medium transition-colors" :class="tab === 'users' ? 'text-yellow-500 border-b-2 border-yellow-500' : 'text-gray-400 hover:text-white'">
                    Usuarios
                </button>
                <button @click="tab = 'teams'" class="pb-2 text-sm font-medium transition-colors" :class="tab === 'teams' ? 'text-yellow-500 border-b-2 border-yellow-500' : 'text-gray-400 hover:text-white'">
                    Grupos
                </button>
                <button @click="tab = 'link'" class="pb-2 text-sm font-medium transition-colors" :class="tab === 'link' ? 'text-yellow-500 border-b-2 border-yellow-500' : 'text-gray-400 hover:text-white'">
                    Enlace Público
                </button>
            </div>

            <div v-if="loadingData" class="flex justify-center py-8">
                <i class="fas fa-spinner fa-spin text-2xl text-yellow-500"></i>
            </div>

            <div v-else>
                <!-- Users Tab -->
                <div v-if="tab === 'users'" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Usuario</label>
                        <select v-model="shareUserForm.user_id" class="w-full bg-black/20 border border-white/10 rounded-lg text-sm text-gray-200 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500">
                            <option value="" disabled class="bg-[#252525] text-gray-400">Seleccionar...</option>
                            <option v-for="user in users" :key="user.id" :value="user.id" class="bg-[#252525] text-white">
                                {{ user.name }} ({{ user.email }})
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Permiso</label>
                        <select v-model="shareUserForm.permission" class="w-full bg-black/20 border border-white/10 rounded-lg text-sm text-gray-200 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500">
                            <option value="view" class="bg-[#252525] text-white">Solo Lectura</option>
                            <option value="edit" class="bg-[#252525] text-white">Editar</option>
                        </select>
                    </div>
                    <button @click="shareWithUser" :disabled="!shareUserForm.user_id || shareUserForm.processing"
                        class="w-full py-2 bg-yellow-600 hover:bg-yellow-500 text-white rounded-lg text-sm font-bold transition-colors disabled:opacity-50">
                        Compartir
                    </button>
                </div>

                <!-- Teams Tab -->
                <div v-if="tab === 'teams'" class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Grupo</label>
                        <select v-model="shareTeamForm.team_id" class="w-full bg-black/20 border border-white/10 rounded-lg text-sm text-gray-200 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500">
                            <option value="" disabled class="bg-[#252525] text-gray-400">Seleccionar...</option>
                            <option v-for="team in teams" :key="team.id" :value="team.id" class="bg-[#252525] text-white">
                                {{ team.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-1">Permiso</label>
                        <select v-model="shareTeamForm.permission" class="w-full bg-black/20 border border-white/10 rounded-lg text-sm text-gray-200 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500">
                            <option value="view" class="bg-[#252525] text-white">Solo Lectura</option>
                            <option value="edit" class="bg-[#252525] text-white">Editar</option>
                        </select>
                    </div>
                    <button @click="shareWithTeam" :disabled="!shareTeamForm.team_id || shareTeamForm.processing"
                        class="w-full py-2 bg-yellow-600 hover:bg-yellow-500 text-white rounded-lg text-sm font-bold transition-colors disabled:opacity-50">
                        Compartir
                    </button>
                </div>

                <!-- Public Link Tab -->
                <div v-if="tab === 'link'" class="space-y-4">
                    <div v-if="!publicLink">
                        <label class="block text-xs font-medium text-gray-400 mb-1">Expiración (Opcional)</label>
                        <input type="date" v-model="publicLinkForm.expires_at" class="w-full bg-black/20 border border-white/10 rounded-lg text-sm text-gray-200 focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 mb-4">
                        
                        <button @click="generateLink" :disabled="publicLinkForm.processing"
                            class="w-full py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-bold transition-colors disabled:opacity-50">
                            Generar Enlace
                        </button>
                    </div>

                    <div v-else class="bg-black/20 p-3 rounded-lg border border-white/10">
                        <div class="flex items-center gap-2 mb-2">
                             <input type="text" readonly :value="publicLink.url" class="flex-1 bg-transparent border-none text-xs text-gray-300 focus:ring-0 p-0 truncate">
                             <button @click="copyLink" class="text-blue-400 text-xs hover:text-blue-300 font-medium">Copiar</button>
                        </div>
                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <span>Expira: {{ publicLink.expires_at ? new Date(publicLink.expires_at).toLocaleDateString() : 'Nunca' }}</span>
                            <button @click="deleteLink" class="text-red-400 hover:text-red-300">Eliminar</button>
                        </div>
                    </div>
                </div>

                <!-- Existing Shares List -->
                <div v-if="existingShares.length > 0" class="mt-6 pt-4 border-t border-white/10">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Accesos Concedidos</h4>
                    <div class="space-y-2 max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                        <div v-for="share in existingShares" :key="share.id" class="flex items-center justify-between bg-white/5 p-2 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-lg">
                                    {{ share.shared_with_team_id ? '👥' : '👤' }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-200">
                                        {{ share.shared_with_team_id ? share.shared_with_team.name : share.shared_with_user.name }}
                                    </p>
                                    <p class="text-[10px] text-gray-500 uppercase">{{ share.permission }}</p>
                                </div>
                            </div>
                            <button @click="removeShare(share.id)" class="text-gray-500 hover:text-red-400 transition-colors">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #444;
    border-radius: 2px;
}
</style>
