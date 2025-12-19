<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import ModelAttachmentsCreator from '@/Components/Cloud/ModelAttachmentsCreator.vue';
import ModelAttachments from '@/Components/Cloud/ModelAttachments.vue';

const props = defineProps({
    alliance: {
        type: Object,
        default: null,
    },
    typeOptions: Array,
});

const form = useForm({
    name: props.alliance?.name || '',
    type_id: props.alliance?.type_id || '',
    start_date: props.alliance?.start_date ? props.alliance.start_date.split('T')[0] : new Date().toISOString().split('T')[0],
    validity: props.alliance?.validity || '',
    certified: props.alliance?.certified ? true : false,
    pending_file_ids: [],
    files: [],
});

const submit = () => {
    if (props.alliance) {
        form.put(route('donations.alliances.update', props.alliance.id), {
            preserveScroll: true,
        });
    } else {
        form.post(route('donations.alliances.store'), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Nombre -->
                <div>
                    <InputLabel for="name" value="Nombre *" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Nombre de la alianza"
                        required
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <!-- Tipo de Alianza -->
                <div>
                    <InputLabel for="type_id" value="Tipo de Alianza" />
                    <select
                        id="type_id"
                        v-model="form.type_id"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    >
                        <option value="">Seleccionar tipo</option>
                        <option v-for="type in typeOptions" :key="type.id" :value="type.id">
                            {{ type.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.type_id" class="mt-2" />
                </div>

                <!-- Fecha de Inicio -->
                <div>
                    <InputLabel for="start_date" value="Fecha de Inicio *" />
                    <TextInput
                        id="start_date"
                        v-model="form.start_date"
                        type="date"
                        class="mt-1 block w-full"
                        required
                    />
                    <InputError :message="form.errors.start_date" class="mt-2" />
                </div>

                <!-- Vigencia (Meses) -->
                <div>
                    <InputLabel for="validity" value="Vigencia (Meses)" />
                    <TextInput
                        id="validity"
                        v-model="form.validity"
                        type="number"
                        min="1"
                        class="mt-1 block w-full"
                        placeholder="Ej: 12"
                    />
                    <InputError :message="form.errors.validity" class="mt-2" />
                </div>

                <!-- Certificado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">¿Certificado?</label>
                    <div class="flex items-center mt-2">
                         <label class="flex items-center">
                            <Checkbox v-model:checked="form.certified" name="certified" />
                            <span class="ml-2 text-sm text-gray-700">Sí, certificado entregado</span>
                        </label>
                    </div>
                    <InputError :message="form.errors.certified" class="mt-2" />
                </div>
            </div>

            <!-- Archivos -->
            <div class="mt-6 border-t pt-6">
                 <div v-if="!alliance">
                    <ModelAttachmentsCreator 
                        v-model:files="form.files"
                        v-model:pendingFileIds="form.pending_file_ids" 
                        area-slug="donaciones"
                    />
                 </div>
                 <div v-else>
                     <ModelAttachments 
                        :model-id="alliance.id"
                        :model-type="String('App\\Models\\Alliance')"
                        area-slug="donaciones"
                     />
                 </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                <SecondaryButton @click="() => $inertia.visit(route('donations.alliances.index'))">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </SecondaryButton>
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    <i class="fas fa-save mr-2"></i>{{ alliance ? 'Actualizar Alianza' : 'Guardar Alianza' }}
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>
