<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

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
                        <TextInput id="name" type="text" v-model="form.name" class="w-full mt-1" required autofocus />
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
                        <InputLabel for="label" value="Etiqueta" />
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
</template>
