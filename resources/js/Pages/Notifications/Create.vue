<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Form from './Form.vue';

defineProps({
    users: Array,
});
</script>

<template>
    <AppLayout title="Crear Notificación">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Crear Notificación
            </h2>
        </template>

        <div>
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Crear Notificación</h1>
                <p class="text-gray-600 mt-1">Configura una nueva notificación automática</p>
            </div>

            <Form :users="users" />
        </div>
    </AppLayout>
</template>
