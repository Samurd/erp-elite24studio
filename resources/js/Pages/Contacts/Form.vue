<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3'; // Import router manually

const props = defineProps({
    isEdit: Boolean,
    contact: Object,
    options: Object, // { contact_types, relation_types, states, sources, labels, users }
});

const form = useForm({
    name: props.contact ? props.contact.name : '',
    empresa: props.contact ? props.contact.company : '', // Mapping 'company' db field to 'empresa' model name used here for consistency if wanted, but simpler to use DB names. Livewire used 'company' var.
    // wait, Validation uses 'company'. Let's use 'company'.
    company: props.contact ? props.contact.company : '',
    contact_type_id: props.contact ? props.contact.contact_type_id : '',
    relation_type_id: props.contact ? props.contact.relation_type_id : '',
    status_id: props.contact ? props.contact.status_id : '',
    email: props.contact ? props.contact.email : '',
    address: props.contact ? props.contact.address : '',
    phone: props.contact ? props.contact.phone : '',
    city: props.contact ? props.contact.city : '',
    // proyecto_asociado: Not in DB validation? Livewire had input but no validation rule for it? Ah, it was just an input "Proyecto Asociado" not bound to anything in getRules()? `class="border rounded px-3 py-2 w-full" />` ... it had NO wire:model! 
    // It was a visual placeholder in Livewire? I will omit it or add it as non-functional to match visual.
    // The livewire file shows: <x-input type="text" id="proyecto_asociado" placeholder="Proyecto Asociado" ... /> NO wire:model.
    
    source_id: props.contact ? props.contact.source_id : '',
    first_contact_date: props.contact ? props.contact.first_contact_date : new Date().toISOString().split('T')[0],
    responsible_id: props.contact ? props.contact.responsible_id : (props.options.users.find(u => u.id === props.auth?.user?.id)?.id || ''), // Default to current user? In livewire mount: $this->responsible_id = $current_user->id;
    label_id: props.contact ? props.contact.label_id : '',
    notes: props.contact ? props.contact.notes : '',
});

const submit = () => {
    if (props.isEdit) {
        form.put(route('contacts.update', props.contact.id));
    } else {
        form.post(route('contacts.store'));
    }
};

// Tag Management
const showTagsModal = ref(false);
const tagForm = useForm({
    id: null,
    name: '',
    color: '#3B82F6', // Default blue-500
    category_slug: 'etiqueta_contacto',
});

const openTagsModal = () => {
    tagForm.reset();
    tagForm.id = null;
    showTagsModal.value = true;
};

const editTag = (tag) => {
    tagForm.id = tag.id;
    tagForm.name = tag.name;
    tagForm.color = tag.color || '#3B82F6';
    // showTagsModal.value = true; // Already open
};

const cancelEditTag = () => {
    tagForm.reset();
    tagForm.id = null;
};

const submitTag = () => {
    if (tagForm.id) {
        tagForm.put(route('tags.update', tagForm.id), {
            preserveScroll: true,
            onSuccess: () => {
                cancelEditTag();
                // Refresh options? Inertia handles props reload on back(), 
                // but we might need to manually ensure options are fresh or just rely on router.reload
                 router.reload({ only: ['options'] });
            }
        });
    } else {
        tagForm.post(route('tags.store'), {
            preserveScroll: true,
            onSuccess: () => {
                cancelEditTag();
                router.reload({ only: ['options'] });
            }
        });
    }
};

const deleteTag = (tag) => {
    if (confirm('¿Eliminar etiqueta?')) {
        router.delete(route('tags.destroy', tag.id), {
             preserveScroll: true,
             onSuccess: () => router.reload({ only: ['options'] })
        });
    }
};
</script>

<template>
    <AppLayout :title="isEdit ? 'Editar Contacto' : 'Registrar Contacto'">
        <main class="flex-1 p-10 bg-gray-100 overflow-y-auto">
            <div class="bg-white shadow-md rounded-lg p-4">
                <h3 class="text-black text-2xl font-bold mb-4">
                    {{ isEdit ? 'Editar contacto' : 'Registrar contacto' }}
                </h3>
                
                <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- Name -->
                    <div>
                        <InputLabel for="name" value="Nombre Contacto" />
                        <TextInput id="name" type="text" v-model="form.name" class="w-full mt-1" required />
                        <InputError :message="form.errors.name" class="mt-1" />
                    </div>

                    <!-- Company -->
                    <div>
                        <InputLabel for="company" value="Empresa" />
                        <TextInput id="company" type="text" v-model="form.company" class="w-full mt-1" required />
                        <InputError :message="form.errors.company" class="mt-1" />
                    </div>

                    <!-- Contact Type -->
                    <div>
                        <InputLabel for="contact_type" value="Tipo de Contacto" />
                        <select id="contact_type" v-model="form.contact_type_id" 
                            class="mt-1 w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2 focus:ring-yellow-500">
                            <option value="">Seleccione...</option>
                            <option v-for="opt in options.contact_types" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
                        </select>
                        <InputError :message="form.errors.contact_type_id" class="mt-1" />
                    </div>

                    <!-- Relation Type -->
                     <div>
                        <InputLabel for="relation_type" value="Tipo de Relación" />
                        <select id="relation_type" v-model="form.relation_type_id" 
                            class="mt-1 w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2 focus:ring-yellow-500">
                            <option value="">Seleccione...</option>
                             <option v-for="opt in options.relation_types" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
                        </select>
                        <InputError :message="form.errors.relation_type_id" class="mt-1" />
                    </div>

                    <!-- Status -->
                    <div>
                        <InputLabel for="status" value="Estado" />
                        <select id="status" v-model="form.status_id"
                            class="mt-1 w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2 focus:ring-yellow-500">
                            <option value="">Seleccione...</option>
                             <option v-for="opt in options.states" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
                        </select>
                        <InputError :message="form.errors.status_id" class="mt-1" />
                    </div>

                    <!-- Email -->
                    <div>
                        <InputLabel for="email" value="Correo electrónico" />
                        <TextInput id="email" type="email" v-model="form.email" class="w-full mt-1" required />
                        <InputError :message="form.errors.email" class="mt-1" />
                    </div>

                    <!-- Address -->
                    <div>
                        <InputLabel for="address" value="Dirección" />
                        <TextInput id="address" type="text" v-model="form.address" class="w-full mt-1" />
                        <InputError :message="form.errors.address" class="mt-1" />
                    </div>

                    <!-- Phone -->
                    <div>
                        <InputLabel for="phone" value="Teléfono" />
                        <TextInput id="phone" type="text" v-model="form.phone" class="w-full mt-1" />
                        <InputError :message="form.errors.phone" class="mt-1" />
                    </div>

                    <!-- City -->
                    <div>
                        <InputLabel for="city" value="Ciudad" />
                        <TextInput id="city" type="text" v-model="form.city" class="w-full mt-1" />
                        <InputError :message="form.errors.city" class="mt-1" />
                    </div>
                    
                    <!-- Project (Visual only as per legacy) -->
                    <div>
                         <InputLabel value="Proyecto Asociado" />
                         <TextInput type="text" class="w-full mt-1" placeholder="Proyecto Asociado" />
                    </div>

                    <!-- Source -->
                    <div>
                        <InputLabel for="source" value="Fuente" />
                        <select id="source" v-model="form.source_id"
                            class="mt-1 w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2 focus:ring-yellow-500">
                            <option value="">Seleccione...</option>
                             <option v-for="opt in options.sources" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
                        </select>
                        <InputError :message="form.errors.source_id" class="mt-1" />
                    </div>

                    <!-- Date -->
                    <div>
                        <InputLabel for="date" value="Fecha Primer Contacto" />
                        <TextInput id="date" type="date" v-model="form.first_contact_date" class="w-full mt-1" />
                        <InputError :message="form.errors.first_contact_date" class="mt-1" />
                    </div>

                    <!-- Responsible -->
                    <div>
                        <InputLabel for="responsible" value="Responsable" />
                        <select id="responsible" v-model="form.responsible_id"
                            class="mt-1 w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2 focus:ring-yellow-500">
                            <option value="">Seleccione...</option>
                             <option v-for="opt in options.users" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
                        </select>
                        <InputError :message="form.errors.responsible_id" class="mt-1" />
                    </div>

                    <!-- Label -->
                    <div>
                        <div class="flex justify-between items-center mb-1">
                             <InputLabel for="label" value="Etiqueta" />
                             <button type="button" @click="openTagsModal" class="text-xs text-blue-600 hover:text-blue-800 hover:underline">
                                 Gestionar
                             </button>
                        </div>
                         <select id="label" v-model="form.label_id"
                            class="mt-1 w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-2 focus:ring-yellow-500">
                            <option value="">Seleccione...</option>
                             <option v-for="opt in options.labels" :key="opt.id" :value="opt.id">{{ opt.name }}</option>
                        </select>
                        <InputError :message="form.errors.label_id" class="mt-1" />
                    </div>

                    <!-- Notes -->
                    <div class="col-span-1 md:col-span-2">
                        <InputLabel for="notes" value="Observaciones" />
                        <textarea id="notes" v-model="form.notes" rows="5"
                             class="mt-1 w-full border border-gray-300 focus:border-yellow-500 rounded-lg p-3 shadow-sm focus:ring-yellow-500"></textarea>
                         <InputError :message="form.errors.notes" class="mt-1" />
                    </div>

                    <!-- Buttons -->
                    <div class="col-span-1 md:col-span-2 flex justify-end space-x-2 mt-4">
                         <Link :href="route('contacts.index')" class="text-gray-600 hover:text-gray-900 mr-4 flex items-center">
                            Cancelar
                        </Link>
                        <PrimaryButton type="submit" :disabled="form.processing">
                            {{ isEdit ? 'Actualizar' : 'Guardar' }} contacto
                        </PrimaryButton>
                    </div>

                </form>
            </div>
        </main>
    </AppLayout>

    <!-- Tags Management Modal -->
    <DialogModal :show="showTagsModal" @close="showTagsModal = false">
        <template #title>
            Gestionar Etiquetas
        </template>
        <template #content>
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">{{ tagForm.id ? 'Editar Etiqueta' : 'Nueva Etiqueta' }}</h4>
                <div class="flex gap-2">
                    <div class="flex-1">
                        <TextInput v-model="tagForm.name" placeholder="Nombre de etiqueta" class="w-full" />
                        <InputError :message="tagForm.errors.name" class="mt-1" />
                    </div>
                    <div class="w-24">
                         <input type="color" v-model="tagForm.color" class="h-10 w-full border border-gray-300 rounded-md cursor-pointer" />
                    </div>
                    <PrimaryButton @click="submitTag" :disabled="tagForm.processing">
                        {{ tagForm.id ? 'Actualizar' : 'Agregar' }}
                    </PrimaryButton>
                    <SecondaryButton v-if="tagForm.id" @click="cancelEditTag">
                        Cancelar
                    </SecondaryButton>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Etiquetas Existentes</h4>
                <div v-if="options.labels.length === 0" class="text-gray-500 text-sm italic">No hay etiquetas creadas.</div>
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    <div v-for="tag in options.labels" :key="tag.id" class="flex items-center justify-between p-2 bg-gray-50 rounded hover:bg-gray-100 group">
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 rounded-full" :style="{ backgroundColor: tag.color || '#ccc' }"></div>
                            <span class="text-gray-900 font-medium">{{ tag.name }}</span>
                        </div>
                        <div class="flex items-center space-x-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                            <button @click="editTag(tag)" class="text-blue-600 hover:text-blue-800 p-1">
                                <i class="fas fa-pen"></i>
                            </button>
                            <button @click="deleteTag(tag)" class="text-red-600 hover:text-red-800 p-1">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template #footer>
            <SecondaryButton @click="showTagsModal = false">
                Cerrar
            </SecondaryButton>
        </template>
    </DialogModal>
</template>
