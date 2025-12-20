<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import VisitForm from './Partials/VisitForm.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    worksite: Object,
    visitStatusOptions: Array,
    visitorOptions: Array,
});
</script>

<template>
    <Head title="Nueva Visita" />

    <AppLayout title="Nueva Visita">
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Nueva Visita
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Registrar nueva visita para la obra
                    </p>
                </div>
                <Link :href="route('worksites.show', worksite.id)" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver a la Obra
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <VisitForm 
                    :worksite="worksite"
                    :visitStatusOptions="visitStatusOptions"
                    :visitorOptions="visitorOptions"
                />
            </div>
        </div>
    </AppLayout>
</template>
