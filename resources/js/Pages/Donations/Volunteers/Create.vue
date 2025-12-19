<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import VolunteerForm from './Form.vue';

defineProps({
    campaigns: Array,
    statusOptions: Array,
});
</script>

<template>
    <AppLayout title="Nuevo Voluntario">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Nuevo Voluntario</h1>
                    <p class="text-gray-600 mt-1">
                        Registrar un nuevo voluntario en el sistema
                    </p>
                </div>
                <div class="flex space-x-3">
                    <Link :href="route('donations.volunteers.index')"
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Volver
                    </Link>
                </div>
            </div>
        </div>

        <VolunteerForm 
            :campaigns="campaigns"
            :status-options="statusOptions"
        />
    </AppLayout>
</template>
