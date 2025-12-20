<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PunchItemForm from './Partials/PunchItemForm.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    worksite: Object,
    punchItem: Object,
    statusOptions: Array,
    responsibles: Array,
});
</script>

<template>
    <Head title="Editar Punch Item" />

    <AppLayout title="Editar Punch Item">
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Editar Punch Item
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Actualiza la información del Punch Item
                    </p>
                    <div class="mt-2">
                        <span class="text-sm text-gray-500">Obra: </span>
                        <span class="text-sm font-medium text-gray-900">{{ worksite.name }}</span>
                    </div>
                </div>
                <Link :href="route('worksites.show', worksite.id)" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <PunchItemForm 
                    :worksite="worksite"
                    :punchItem="punchItem"
                    :statusOptions="statusOptions"
                    :responsibles="responsibles"
                />
            </div>
        </div>
    </AppLayout>
</template>
