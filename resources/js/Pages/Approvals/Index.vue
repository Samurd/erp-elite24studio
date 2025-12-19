<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import ApprovalTable from './ApprovalTable.vue'; 

const props = defineProps({
    received_approvals: Object,
    received_buy_approvals: Object,
    approvals_sent: Object,
    buy_approvals_sent: Object,
});

const mainTab = ref('recibida');
const subTab = ref('aprobaciones');

// --- Modal Logic ---
const showModal = ref(false);
const selectedApproval = ref(null);
const isLoadingDetails = ref(false);
const comment = ref('');

const openModal = async (id) => {
    showModal.value = true;
    isLoadingDetails.value = true;
    selectedApproval.value = null;
    comment.value = '';

    try {
        const response = await axios.get(route('approvals.show', id));
        selectedApproval.value = response.data;
    } catch (error) {
        console.error("Error fetching approval", error);
    } finally {
        isLoadingDetails.value = false;
    }
};

const closeModal = () => {
    showModal.value = false;
    selectedApproval.value = null;
};

// --- Actions ---
const actionForm = useForm({
    comment: ''
});

const approve = () => {
    if (!selectedApproval.value) return;
    actionForm.comment = comment.value;
    actionForm.post(route('approvals.approve', selectedApproval.value.id), {
        onSuccess: () => closeModal(),
        preserveScroll: true
    });
};

const reject = () => {
    if (!selectedApproval.value) return;
    actionForm.comment = comment.value;
    actionForm.post(route('approvals.reject', selectedApproval.value.id), {
        onSuccess: () => closeModal(),
        preserveScroll: true
    });
};

const deleteApproval = (id) => {
    if(confirm('¿Estás seguro?')) {
        router.delete(route('approvals.destroy', id), {
            preserveScroll: true
        });
    }
}

const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleString('es-ES');
};

const getStatusColor = (name) => {
    if (name === 'Aprobado') return 'bg-green-100 text-green-800';
    if (name === 'Rechazado') return 'bg-red-100 text-red-800';
    if (name === 'En espera') return 'bg-yellow-100 text-yellow-800';
    return 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <AppLayout title="Aprobaciones">
        <!-- Main Content -->
        <main class="flex-1 p-10 bg-gray-100 font-sans">
            <div class="bg-white rounded-lg shadow border border-gray-200">
                
                <!-- Main Tabs -->
                <div class="flex justify-between items-center border-b border-gray-200 text-sm px-5">
                    <div class="flex">
                        <button class="px-5 py-3 focus:outline-none"
                            :class="mainTab === 'recibida' ? 'border-b-2 border-yellow-700 font-semibold text-gray-900' : 'text-gray-600 hover:text-gray-900'"
                            @click="mainTab = 'recibida'; subTab = 'aprobaciones'">
                            Recibida
                        </button>
                        <button class="px-5 py-3 focus:outline-none"
                            :class="mainTab === 'enviada' ? 'border-b-2 border-yellow-700 font-semibold text-gray-900' : 'text-gray-600 hover:text-gray-900'"
                            @click="mainTab = 'enviada'; subTab = 'aprobaciones'">
                            Enviada
                        </button>
                    </div>
                </div>

                <!-- Sub Tabs & Actions -->
                <div class="flex flex-wrap justify-between items-center gap-3 border-b border-gray-200 px-5 py-3">
                    <div class="flex space-x-6 text-sm font-medium">
                        <button @click="subTab = 'aprobaciones'"
                            :class="subTab === 'aprobaciones' ? 'text-yellow-700 border-b-[3px] border-yellow-700 pb-1' : 'text-gray-400 hover:text-gray-600'">Aprobaciones</button>
                         <button @click="subTab = 'contratos'"
                            :class="subTab === 'contratos' ? 'text-yellow-700 border-b-[3px] border-yellow-700 pb-1' : 'text-gray-400 hover:text-gray-600'">Contratos/Firmas</button>
                         <button @click="subTab = 'solicitud'"
                            :class="subTab === 'solicitud' ? 'text-yellow-700 border-b-[3px] border-yellow-700 pb-1' : 'text-gray-400 hover:text-gray-600'">Solicitud de Compra</button>
                    </div>
                     <div class="flex items-center space-x-3">
                        <Link :href="route('approvals.create')"
                            class="bg-yellow-700 hover:bg-yellow-800 text-white px-4 py-2 rounded-md text-sm font-medium shadow-sm">
                            + Nueva solicitud de aprobación
                        </Link>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="p-5">
                    
                    <!-- TAB: RECIBIDA -->
                    <div v-if="mainTab === 'recibida'">
                        <!-- SubTab: Aprobaciones (Not Buy) -->
                        <div v-if="subTab === 'aprobaciones'">
                            <ApprovalTable :approvals="received_approvals" @view="openModal" />
                        </div>
                         <!-- SubTab: Contratos -->
                        <div v-if="subTab === 'contratos'">
                             <iframe src="https://elite-24-studio-sas.odoo.com/odoo/sign-documents" width="100%" height="800" frameborder="0" style="border: none;"></iframe>
                        </div>
                        <!-- SubTab: Solicitud (Buy) -->
                        <div v-if="subTab === 'solicitud'">
                            <ApprovalTable :approvals="received_buy_approvals" @view="openModal" />
                        </div>
                    </div>

                    <!-- TAB: ENVIADA -->
                    <div v-if="mainTab === 'enviada'">
                         <div v-if="subTab === 'aprobaciones'">
                            <ApprovalTable :approvals="approvals_sent" :is-sent="true" @view="openModal" @remove="deleteApproval" />
                        </div>
                        <div v-if="subTab === 'contratos'">
                             <iframe src="https://elite-24-studio-sas.odoo.com/odoo/sign-documents" width="100%" height="800" frameborder="0" style="border: none;"></iframe>
                        </div>
                        <div v-if="subTab === 'solicitud'">
                            <ApprovalTable :approvals="buy_approvals_sent" :is-sent="true" @view="openModal" @remove="deleteApproval" />
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <!-- Details Modal -->
        <DialogModal :show="showModal" @close="closeModal">
            <template #title>
                 <div class="flex items-center gap-2">
                    <div class="bg-yellow-500 text-white p-2 rounded">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <span>Detalles de la solicitud</span>
                </div>
            </template>
            <template #content>
                 <div v-if="isLoadingDetails" class="text-center py-10">
                    <p class="text-gray-500">Cargando...</p>
                 </div>
                 <div v-else-if="selectedApproval" class="space-y-4">
                     <!-- State -->
                     <div>
                        <span class="text-sm text-gray-500">Estado: </span>
                        <span :class="['px-2 py-1 rounded text-xs font-semibold', getStatusColor(selectedApproval.status ? selectedApproval.status.name : '')]">
                            {{ selectedApproval.status ? selectedApproval.status.name : 'N/A' }}
                        </span>
                     </div>
                     <!-- Info -->
                      <div>
                        <h3 class="text-lg font-semibold">{{ selectedApproval.name }}</h3>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ selectedApproval.description }}</p>
                      </div>
                      
                      <!-- Approvers List -->
                      <div>
                         <h4 class="font-semibold text-gray-800">Aprobadores</h4>
                         <ul class="mt-2 space-y-1 text-sm w-full">
                            <li v-for="approver in selectedApproval.approvers" :key="approver.id" 
                                class="flex flex-col items-start gap-2 w-full bg-gray-100 rounded-lg p-2">
                                <div class="flex items-center justify-between gap-2 w-full">
                                    <div class="flex items-center gap-2">
                                        <!-- Simple generic avatar if no photo -->
                                        <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-xs">
                                             {{ approver.user.name.charAt(0) }}
                                        </div>
                                        <span>{{ approver.user.name }}</span>
                                    </div>
                                    <span class="ml-auto text-gray-500 text-xs font-bold">
                                         {{ approver.status ? approver.status.name : 'Pendiente' }}
                                    </span>
                                </div>
                                <div v-if="approver.comment" class="p-2 w-full bg-white rounded border border-gray-200 text-gray-600 italic">
                                    "{{ approver.comment }}"
                                </div>
                            </li>
                         </ul>
                      </div>
                      
                      <!-- Files -->
                       <div v-if="selectedApproval.files && selectedApproval.files.length">
                             <h4 class="font-semibold text-gray-800 mb-2">Archivos adjuntos</h4>
                             <ul class="space-y-2">
                                <li v-for="file in selectedApproval.files" :key="file.id">
                                     <a :href="'/storage/' + file.path" target="_blank" class="text-blue-600 hover:underline flex items-center gap-2">
                                        <i class="fas fa-file"></i> {{ file.name }}
                                     </a>
                                </li>
                             </ul>
                       </div>

                       <!-- Action Input -->
                       <div>
                            <label class="block text-sm font-medium text-gray-700">Comentarios</label>
                            <textarea v-model="comment" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-yellow-500 mt-1" rows="3" placeholder="Agregue sus comentarios aquí..."></textarea>
                       </div>
                 </div>
            </template>
            <template #footer>
                 <div class="flex gap-2 justify-end w-full">
                    <SecondaryButton @click="closeModal">Cerrar</SecondaryButton>
                    <PrimaryButton @click="approve" class="bg-green-600 hover:bg-green-700 border-none">Aprobar</PrimaryButton>
                    <DangerButton @click="reject">Rechazar</DangerButton>
                 </div>
            </template>
        </DialogModal>
    </AppLayout>
</template>
