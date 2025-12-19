<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Form from './Form.vue';

defineProps({
    notification: Object,
    users: Array,
});
</script>

<template>
    <AppLayout title="Editar Notificación">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Notificación
            </h2>
        </template>

        <div>
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Editar Notificación</h1>
                <p class="text-gray-600 mt-1">Modifica la configuración de la notificación</p>
            </div>

            <Form :template="notification" :users="users" />
        </div>
    </AppLayout>
</template>
