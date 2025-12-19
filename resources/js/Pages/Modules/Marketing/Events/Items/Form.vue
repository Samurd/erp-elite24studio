<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import MoneyInput from '@/Components/MoneyInput.vue';
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue';
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue';

const props = defineProps({
    event: Object, // Parent event (available in create & edit)
    item: Object, // Item (only in edit)
    unitOptions: Array,
});

const isEdit = computed(() => !!props.item);

const form = useForm({
    _method: isEdit.value ? 'PUT' : 'POST',
    description: props.item?.description || '',
    quantity: props.item?.quantity || '',
    unit_id: props.item?.unit_id || '',
    unit_price: props.item?.unit_price || '',
    total_price: props.item?.total_price || '',
    files: [],
    pending_file_ids: [],
});

const calculateTotal = () => {
    const qty = parseFloat(form.quantity) || 0;
    const price = parseFloat(form.unit_price) || 0;
    form.total_price = qty * price;
};

const submit = () => {
    if (isEdit.value) {
        form.post(route('marketing.events.items.update', [props.event.id, props.item.id]), {
            forceFormData: true,
        });
    } else {
        form.post(route('marketing.events.items.store', props.event.id), {
            forceFormData: true,
        });
    }
};

const handleFileChange = (e) => {
    form.files = Array.from(e.target.files);
};
</script>

<template>
    <AppLayout :title="isEdit ? 'Editar Ítem' : 'Nuevo Ítem'">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ isEdit ? 'Editar Ítem' : 'Nuevo Ítem' }}
                    </h1>
                     <p class="text-gray-600 mt-1">
                        Evento: <span class="font-semibold">{{ event.name }}</span>
                    </p>
                </div>
                 <div class="flex space-x-3">
                    <Link :href="route('marketing.events.show', event.id)"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Volver al Evento
                    </Link>
                </div>
            </div>
        </div>

         <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Descripción -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción del Ítem *</label>
                        <input type="text" id="description" v-model="form.description"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                            placeholder="Ej: Alquiler de sonido...">
                        <div v-if="form.errors.description" class="text-red-500 text-sm mt-1">{{ form.errors.description }}</div>
                    </div>

                    <!-- Cantidad -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Cantidad *</label>
                        <input type="number" id="quantity" v-model="form.quantity" @input="calculateTotal"
                            step="0.01" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <div v-if="form.errors.quantity" class="text-red-500 text-sm mt-1">{{ form.errors.quantity }}</div>
                    </div>

                    <!-- Unidad -->
                    <div>
                        <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-2">Unidad de Medida *</label>
                        <select id="unit_id" v-model="form.unit_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Seleccionar unidad</option>
                            <option v-for="unit in unitOptions" :key="unit.id" :value="unit.id">{{ unit.name }}</option>
                        </select>
                        <div v-if="form.errors.unit_id" class="text-red-500 text-sm mt-1">{{ form.errors.unit_id }}</div>
                    </div>

                    <!-- Valor Unitario -->
                    <div>
                        <MoneyInput
                            id="unit_price"
                            v-model="form.unit_price"
                            label="Valor Unitario *"
                            placeholder="$0.00"
                            :error="form.errors.unit_price"
                            @update:modelValue="calculateTotal"
                        />
                    </div>

                     <!-- Valor Total (Calculated but Editable) -->
                    <div>
                         <MoneyInput
                            id="total_price"
                            v-model="form.total_price"
                            label="Valor Total"
                            placeholder="$0.00"
                            :error="form.errors.total_price"
                        />
                    </div>
                </div>

                 <div class="border-t pt-4 mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Archivos Adjuntos (Cotizaciones, Facturas, etc.)</label>
                    
                     <!-- Edit Mode -->
                     <div v-if="isEdit">
                        <ModelAttachments 
                            :model-id="item.id" 
                            model-type="App\Models\EventItem" 
                            area-slug="marketing" 
                            :files="item.files" 
                        />
                     </div>

                     <!-- Create Mode -->
                     <div v-else>
                        <ModelAttachmentsCreator 
                            model-type="App\Models\EventItem"
                            area-slug="marketing"
                            v-model:files="form.files"
                            v-model:pendingFileIds="form.pending_file_ids"
                        />
                        <p v-if="form.errors.files" class="text-red-500 text-sm mt-1">{{ form.errors.files }}</p>
                     </div>
                 </div>

                 <!-- Botones de Acción -->
                <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                    <Link :href="route('marketing.events.show', event.id)"
                        class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </Link>
                    <button type="submit"
                        :disabled="form.processing"
                        class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors disabled:opacity-50">
                        <i class="fas fa-save mr-2"></i>{{ isEdit ? 'Actualizar' : 'Guardar' }} Ítem
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
