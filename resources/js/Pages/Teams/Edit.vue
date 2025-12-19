<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import TeamForm from './Form.vue';

const props = defineProps({
    team: Object,
});
</script>

<template>
    <AppLayout title="Editar Equipo">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Equipo: {{ team.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="max-w-2xl mx-auto">
                    <TeamForm :team="team" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
