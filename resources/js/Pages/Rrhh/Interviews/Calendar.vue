<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import interactionPlugin from '@fullcalendar/interaction'

const props = defineProps({
    events: Array,
})

const calendarOptions = ref({
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay'
    },
    events: props.events,
    eventClick: (info) => {
        router.visit(route('rrhh.interviews.edit', info.event.id))
    },
    locale: 'es',
})
</script>

<template>
    <AppLayout title="Calendario de Entrevistas">
        <div class="max-w-7xl mx-auto p-6">
             <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Calendario de Entrevistas</h1>
                    <p class="text-gray-600 mt-1">Vista mensual y semanal de entrevistas programadas</p>
                </div>
                 <div class="flex gap-2">
                     <Link
                        :href="route('rrhh.interviews.index')"
                        class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors flex items-center shadow-sm"
                    >
                        <i class="fas fa-list mr-2"></i> Vista Lista
                    </Link>
                    <Link
                        :href="route('rrhh.interviews.create')"
                        class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors flex items-center shadow-sm"
                    >
                        <i class="fas fa-plus mr-2"></i> Agendar Entrevista
                    </Link>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-4 h-[700px]">
                <FullCalendar :options="calendarOptions" class="h-full" />
            </div>
        </div>
    </AppLayout>
</template>

<style>
/* Custom FullCalendar Styles */
.fc-event {
    cursor: pointer;
}
.fc-toolbar-title {
    font-size: 1.25rem !important;
    font-weight: 600 !important;
}
.fc-button-primary {
    background-color: #ca8a04 !important; /* Yellow-600 */
    border-color: #ca8a04 !important;
}
.fc-button-primary:hover {
    background-color: #a16207 !important; /* Yellow-700 */
    border-color: #a16207 !important;
}
.fc-button-active {
    background-color: #854d0e !important; /* Yellow-800 */
    border-color: #854d0e !important;
}
</style>
