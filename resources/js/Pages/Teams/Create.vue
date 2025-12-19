<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TeamForm from './Form.vue';

</script>

<template>
    <AppLayout title="Crear Equipo">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Crear Nuevo Equipo
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="max-w-2xl mx-auto">
                    <TeamForm />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
