<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue'; // fix: restore AppLayout import
import MoneyInput from '@/Components/MoneyInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue';
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue';

const props = defineProps({
    quote: Object,
    contacts: Array,
    statusOptions: Array,
});

const form = useForm({
    _method: props.quote ? 'PUT' : 'POST',
    contact_id: props.quote?.contact_id || '',
    issued_at: props.quote?.issued_at ? props.quote.issued_at.split('T')[0] : new Date().toISOString().split('T')[0],
    status_id: props.quote?.status_id || '',
    total: props.quote?.total || 0,
    files: [],
    pending_file_ids: [],
});

const submit = () => {
    if (props.quote) {
        form.post(route('quotes.update', props.quote.id), {
            forceFormData: true,
            preserveScroll: true,
        });
    } else {
        form.post(route('quotes.store'), {
            forceFormData: true,
            preserveScroll: true,
        });
    }
};

const handleFileChange = (e) => {
    form.files = Array.from(e.target.files);
};
</script>

<template>
    <AppLayout :title="quote ? 'Editar Cotización' : 'Nueva Cotización'">
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ quote ? 'Editar Cotización' : 'Nueva Cotización' }}
                    </h1>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Contacto -->
                        <div>
                            <label for="contact_id" class="block text-sm font-medium text-gray-700 mb-2">Contacto</label>
                            <select id="contact_id" v-model="form.contact_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                <option value="">Seleccione un contacto</option>
                                <option v-for="contact in contacts" :key="contact.id" :value="contact.id">
                                    {{ contact.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.contact_id" class="text-red-500 text-sm mt-1">{{ form.errors.contact_id }}</p>
                        </div>

                        <!-- Fecha de Emisión -->
                        <div>
                            <label for="issued_at" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Emisión</label>
                            <input type="date" id="issued_at" v-model="form.issued_at"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <p v-if="form.errors.issued_at" class="text-red-500 text-sm mt-1">{{ form.errors.issued_at }}</p>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                            <select id="status_id" v-model="form.status_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                                <option value="">Seleccione un estado</option>
                                <option v-for="status in statusOptions" :key="status.id" :value="status.id">
                                    {{ status.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.status_id" class="text-red-500 text-sm mt-1">{{ form.errors.status_id }}</p>
                        </div>

                        <!-- Total -->
                        <div>
                             <MoneyInput 
                                id="total" 
                                v-model="form.total" 
                                label="Total" 
                                :error="form.errors.total" 
                             />
                        </div>
                    </div>

                    <!-- Attachments -->
                     <div class="border-t pt-6 mt-6">
                         <h3 class="text-lg font-medium text-gray-900 mb-4">Archivos Adjuntos</h3>
                         
                         <!-- Edit Mode -->
                         <div v-if="quote">
                            <ModelAttachments 
                                :model-id="quote.id" 
                                model-type="App\Models\Quote" 
                                area-slug="cotizaciones" 
                                :files="quote.files" 
                            />
                         </div>

                         <!-- Create Mode -->
                         <div v-else>
                            <ModelAttachmentsCreator 
                                model-type="App\Models\Quote"
                                area-slug="cotizaciones"
                                v-model:files="form.files"
                                v-model:pendingFileIds="form.pending_file_ids"
                            />
                            <p v-if="form.errors.files" class="text-red-500 text-sm mt-1">{{ form.errors.files }}</p>
                         </div>
                     </div>

                    <div class="flex justify-end space-x-3 pt-6 border-t mt-6">
                        <Link :href="route('quotes.index')"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancelar
                        </Link>
                        <button type="submit" :disabled="form.processing"
                            class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50">
                            {{ quote ? 'Actualizar Cotización' : 'Guardar Cotización' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
