<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    contacts: Object,
    labels: Array,
    users: Array,
    filters: Object,
});

// --- Filter Logic ---
const search = ref(props.filters.search || '');
const empresa = ref(props.filters.empresa || '');
const etiqueta = ref(props.filters.etiqueta || '');
const responsable = ref(props.filters.responsable || '');

const updateFilters = debounce(() => {
    router.get(route('contacts.index'), {
        search: search.value,
        empresa: empresa.value,
        etiqueta: etiqueta.value,
        responsable: responsable.value,
    }, { preserveState: true, preserveScroll: true });
}, 300);

watch([search, empresa, responsable, etiqueta], () => {
    updateFilters();
});

const resetFilters = () => {
    search.value = '';
    empresa.value = '';
    etiqueta.value = '';
    responsable.value = '';
    // Watcher will trigger update
};

// --- Modal Logic ---
const isOpenInfoModal = ref(false);
const selectedContact = ref(null);

const openInfoModal = (contact) => {
    selectedContact.value = contact;
    isOpenInfoModal.value = true;
};

const closeInfoModal = () => {
    isOpenInfoModal.value = false;
    setTimeout(() => selectedContact.value = null, 200);
};

// --- Delete Logic ---
const isOpenDeleteModal = ref(false);
const contactToDelete = ref(null);
const deleteForm = useForm({});

const openDeleteModal = (contact) => {
    contactToDelete.value = contact;
    isOpenDeleteModal.value = true;
};

const closeDeleteModal = () => {
    isOpenDeleteModal.value = false;
    contactToDelete.value = null;
};

const deleteContact = () => {
    if (!contactToDelete.value) return;
    deleteForm.delete(route('contacts.destroy', contactToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => closeDeleteModal(),
    });
};

const formatDate = (dateString) => {
    if (!dateString) return '—';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric' });
};
</script>

<template>
    <AppLayout title="Contactos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Contactos
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Filters Section -->
                <div class="bg-white shadow-md rounded-lg p-4">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Filtros de busqueda</h2>
                    <input type="text" v-model="search" placeholder="Buscar por nombre o correo"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500 mb-4">

                    <div class="flex flex-wrap items-center gap-3">
                        <input type="text" v-model="empresa" placeholder="Empresa"
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-yellow-500 focus:border-yellow-500">



                        <select v-model="etiqueta"
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-yellow-500 focus:border-yellow-500">
                            <option value="">Etiqueta</option>
                            <option v-for="label in labels" :key="label.id" :value="label.id">{{ label.name }}</option>
                        </select>

                        <select v-model="responsable"
                            class="border border-gray-300 rounded-lg text-sm px-3 py-2 focus:ring-yellow-500 focus:border-yellow-500">
                            <option value="">Responsable</option>
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>

                        <button @click="resetFilters"
                            class="px-4 py-2 bg-yellow-500 text-white text-sm rounded-lg hover:bg-yellow-600 transition">
                            Limpiar filtros
                        </button>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="bg-white shadow-md rounded-lg p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Base de Datos</h2>
                        
                        <!-- TODO: Add permission check if needed (passed from backend or use helper) -->
                         <!-- Assuming permissions are handled by middleware or passing prop can-create -->
                        <Link :href="route('contacts.create')"
                            class="bg-black hover:bg-gray-950 text-white px-4 py-2 rounded-lg text-sm shadow flex items-center">
                            <i class="fas fa-plus mr-2"></i> Nuevo
                        </Link>
                    </div>

                    <div class="overflow-x-auto bg-white rounded-lg shadow">
                        <table class="min-w-full text-sm text-left text-gray-600">
                            <thead class="text-xs text-white uppercase bg-gradient-to-r from-black via-yellow-700 to-amber-500">
                                <tr>
                                    <th class="px-6 py-3">Nombre</th>
                                    <th class="px-6 py-3">Etiqueta</th>
                                    <th class="px-6 py-3">Email Personal</th>
                                    <th class="px-6 py-3">Email Corporativo</th>
                                    <th class="px-6 py-3">Empresa</th>
                                    <th class="px-6 py-3">Estado</th>
                                    <th class="px-6 py-3">Responsable</th>
                                    <th class="px-6 py-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="contact in contacts.data" :key="contact.id" class="border-b hover:bg-gray-50">
                                    <td class="px-6 py-3">{{ contact.name }}</td>
                                    <td class="px-6 py-3">
                                        <span v-if="contact.label" class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-semibold" :style="{ backgroundColor: contact.label.color ? contact.label.color + '20' : '#f3f4f6', color: contact.label.color || '#1f2937' }">
                                            {{ contact.label.name }}
                                        </span>
                                        <span v-else>—</span>
                                    </td>
                                    <td class="px-6 py-3">{{ contact.email_personal || '—' }}</td>
                                    <td class="px-6 py-3">{{ contact.email_corporativo || '—' }}</td>
                                    <td class="px-6 py-3">{{ contact.company || '—' }}</td>
                                    <td class="px-6 py-3">{{ contact.status ? contact.status.name : '—' }}</td>
                                    <td class="px-6 py-3">{{ contact.responsible ? contact.responsible.name : '—' }}</td>
                                    <td class="px-6 py-3 flex flex-wrap gap-2 items-center">
                                        <button @click="openInfoModal(contact)" class="text-gray-600 hover:text-gray-900 hover:underline uppercase text-xs font-semibold">
                                            Ver
                                        </button>
                                        
                                        <Link :href="route('contacts.edit', contact.id)" class="text-blue-600 hover:text-blue-900 hover:underline uppercase text-xs font-semibold">
                                            Editar
                                        </Link>

                                        <button @click="openDeleteModal(contact)" class="text-red-500 hover:text-red-700 hover:underline uppercase text-xs font-semibold">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="contacts.data.length === 0">
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-400">No hay resultados</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <Pagination :links="contacts.links" />
                    </div>
                </div>

            </div>
        </div>

        <!-- Info Modal -->
        <DialogModal :show="isOpenInfoModal" @close="closeInfoModal">
            <template #title>
                <div class="flex items-center space-x-3">
                    <h2 class="text-xl font-semibold text-gray-800">Información del Contacto</h2>
                </div>
            </template>
            <template #content>
                <div v-if="selectedContact" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nombre</p>
                            <p class="font-medium text-gray-900">{{ selectedContact.name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email Personal</p>
                            <p class="font-medium text-gray-900">{{ selectedContact.email_personal || '—' }}</p>
                        </div>
                         <div>
                            <p class="text-sm text-gray-500">Email Corporativo</p>
                            <p class="font-medium text-gray-900">{{ selectedContact.email_corporativo || '—' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Empresa</p>
                            <p class="font-medium text-gray-900">{{ selectedContact.company }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Teléfono</p>
                            <p class="font-medium text-gray-900">{{ selectedContact.phone }}</p>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Tipo de contacto</p>
                            <p class="font-medium text-gray-900">{{ selectedContact.contact_type ? selectedContact.contact_type.name : '—' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tipo de relación</p>
                            <p class="font-medium text-gray-900">{{ selectedContact.relation_type ? selectedContact.relation_type.name : '—' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Estado</p>
                            <p class="font-medium text-gray-900">{{ selectedContact.status ? selectedContact.status.name : '—' }}</p>
                        </div>
                         <div>
                            <p class="text-sm text-gray-500">Fuente</p>
                            <p class="font-medium text-gray-900">{{ selectedContact.source ? selectedContact.source.name : '—' }}</p>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Fecha del primer contacto</p>
                            <p class="font-medium text-gray-900">{{ formatDate(selectedContact.first_contact_date) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Responsable</p>
                            <p class="font-medium text-gray-900">{{ selectedContact.responsible ? selectedContact.responsible.name : '—' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Etiqueta</p>
                             <span class="inline-block px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-500 ">
                                {{ selectedContact.label ? selectedContact.label.name : '—' }}
                            </span>
                        </div>
                    </div>
                    <div v-if="selectedContact.notes">
                        <p class="text-sm text-gray-500">Observaciones</p>
                        <p class="mt-1 p-3 rounded-md bg-gray-50 text-gray-700 text-sm">
                            {{ selectedContact.notes }}
                        </p>
                    </div>
                </div>
            </template>
            <template #footer>
                <SecondaryButton @click="closeInfoModal">Cerrar</SecondaryButton>
            </template>
        </DialogModal>

        <!-- Delete Modal -->
        <DialogModal :show="isOpenDeleteModal" @close="closeDeleteModal">
            <template #title>
                Eliminar Contacto
            </template>
            <template #content>
                <div v-if="contactToDelete">
                    ¿Estás seguro de que deseas eliminar a <b>{{ contactToDelete.name }}</b>?
                    <p class="mt-2 text-red-600 text-sm">Esta acción no se puede deshacer.</p>

                    <!-- Warning about related cases if implementation allows checking relations in frontend or if passed from backend. 
                         The original code checked $selectedContact->cases. 
                         Since we pass eager loaded relations or if we want to check cases we might need to load them. 
                         For now, displaying generic warning. -->
                </div>
            </template>
            <template #footer>
                <SecondaryButton @click="closeDeleteModal">Cancelar</SecondaryButton>
                <DangerButton class="ml-3" @click="deleteContact" :disabled="deleteForm.processing">
                    Eliminar
                </DangerButton>
            </template>
        </DialogModal>

    </AppLayout>
</template>
