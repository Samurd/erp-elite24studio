<script setup>
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    show: Boolean,
    categorySlug: String,
    title: String,
    options: Array, // Expected to be passed in from parent, or we can reload page to get fresh props, but props is better for simple lists. 
                   // However, modifying tags will require page reload or event emission to update parent options. 
                   // Ideally we emit an event to parent to refresh options.
});

const emit = defineEmits(['close', 'refresh']);

const form = useForm({
    name: '',
    category_slug: props.categorySlug,
    color: '#CCCCCC',
});

const editingTag = ref(null);
const editForm = useForm({
    name: '',
    color: '#CCCCCC',
});

// Create
const createTag = () => {
    form.post(route('tags.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('name');
            emit('refresh'); // Signal parent to reload props if needed
        },
    });
};

// Start Editing
const startEdit = (tag) => {
    editingTag.value = tag;
    editForm.name = tag.name;
    editForm.color = tag.color;
};

// Cancel Edit
const cancelEdit = () => {
    editingTag.value = null;
    editForm.reset();
};

// Update
const updateTag = () => {
    editForm.put(route('tags.update', editingTag.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            editingTag.value = null;
            editForm.reset();
            emit('refresh');
        },
    });
};

// Delete
const deleteTag = (tag) => {
    if (!confirm('¿Eliminar esta etiqueta?')) return;
    router.delete(route('tags.destroy', tag.id), {
        preserveScroll: true,
        onSuccess: () => emit('refresh'),
    });
};

const close = () => {
    emit('close');
};
</script>

<template>
    <DialogModal :show="show" @close="close">
        <template #title>
            Gestionar {{ title }}
        </template>

        <template #content>
            <!-- Create Form -->
            <div class="mb-6 flex gap-2 items-end border-b pb-4">
                <div class="flex-1">
                    <InputLabel value="Nueva etiqueta" />
                    <TextInput v-model="form.name" type="text" class="w-full mt-1" placeholder="Nombre..." />
                    <InputError :message="form.errors.name" class="mt-1" />
                </div>
                <PrimaryButton @click="createTag" :disabled="form.processing">
                    <i class="fas fa-plus mr-1"></i> Agregar
                </PrimaryButton>
            </div>

            <!-- List -->
            <div class="space-y-2 max-h-60 overflow-y-auto">
                <div v-if="options.length === 0" class="text-gray-400 text-center italic py-2">
                    No hay etiquetas creadas.
                </div>
                
                <div v-for="tag in options" :key="tag.id" class="flex justify-between items-center bg-gray-50 p-2 rounded">
                    
                    <div v-if="editingTag && editingTag.id === tag.id" class="flex-1 flex gap-2 items-center">
                        <TextInput v-model="editForm.name" class="w-full h-8 text-sm" />
                        <button @click="updateTag" class="text-green-600 hover:text-green-800"><i class="fas fa-check"></i></button>
                        <button @click="cancelEdit" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times"></i></button>
                    </div>

                    <div v-else class="flex items-center gap-2">
                         <span class="text-gray-800">{{ tag.name }}</span>
                    </div>

                    <div v-if="!editingTag || editingTag.id !== tag.id" class="flex gap-2">
                        <button @click="startEdit(tag)" class="text-blue-500 hover:text-blue-700 text-sm">
                            <i class="fas fa-edit"></i>
                        </button>
                         <button @click="deleteTag(tag)" class="text-red-500 hover:text-red-700 text-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="close">
                Cerrar
            </SecondaryButton>
        </template>
    </DialogModal>
</template>
