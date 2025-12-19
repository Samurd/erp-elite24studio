<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted, watch } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import FullCalendar from '@fullcalendar/vue3';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import interactionPlugin from '@fullcalendar/interaction';
import esLocale from '@fullcalendar/core/locales/es';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    events: Array,
});

const activeFilter = ref('all');
const calendarOptions = ref({
    plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    locale: esLocale,
    headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
    },
    buttonText: {
        today: 'Hoy',
        month: 'Mes',
        week: 'Semana',
        day: 'Día',
        list: 'Lista'
    },
    events: props.events,
    editable: true,
    selectable: true,
    selectMirror: true,
    dayMaxEvents: true,
    weekends: true,
    select: handleDateSelect,
    eventClick: handleEventClick,
    eventDrop: handleEventDrop,
    eventResize: handleEventResize,
    height: 'auto',
    contentHeight: 650,
});

// Modal State
const showEventModal = ref(false);
const showDeleteConfirm = ref(false);
const isEditing = ref(false);

const form = useForm({
    id: null,
    title: '',
    description: '',
    start: '',
    end: '',
    is_all_day: false,
    color: '#3b82f6',
});

// Detailed Popover State
const showDetailsModal = ref(false);
const selectedEventDetails = ref(null);

function handleDateSelect(selectInfo) {
    if (activeFilter.value !== 'all' && activeFilter.value !== 'Personal') {
        // Prevent creating events when filtered to non-personal types
        return;
    }

    isEditing.value = false;
    form.reset();
    form.start = selectInfo.startStr.slice(0, 16); // format for datetime-local
    form.end = selectInfo.endStr ? selectInfo.endStr.slice(0, 16) : selectInfo.startStr.slice(0, 16);
    form.is_all_day = selectInfo.allDay;
    form.color = '#3b82f6';
    
    showEventModal.value = true;
}

function handleEventClick(clickInfo) {
    const event = clickInfo.event;
    const props = event.extendedProps;

    if (props.isPersonal) {
        // Edit Personal Event
        isEditing.value = true;
        form.id = event.id;
        form.title = event.title;
        form.description = props.description;
        
        // Handle timezone offsets for input
        // FullCalendar dates are Date objects. We need local ISO string.
        // We can just use the ISO string if available or construct it manually to preserve local time.
        
        const formatDate = (date) => {
            if (!date) return '';
            const offset = date.getTimezoneOffset() * 60000;
            return new Date(date.getTime() - offset).toISOString().slice(0, 16);
        };

        form.start = formatDate(event.start);
        form.end = formatDate(event.end || event.start);
        form.is_all_day = event.allDay;
        form.color = event.backgroundColor;

        showEventModal.value = true;
    } else {
        // Show Details for Other Events
        selectedEventDetails.value = {
            title: event.title,
            start: event.start,
            end: event.end,
            allDay: event.allDay,
            color: event.backgroundColor,
            ...props
        };
        showDetailsModal.value = true;
    }
}

function handleEventDrop(dropInfo) {
    const event = dropInfo.event;
    if (event.extendedProps.isPersonal) {
        // Update Personal Event via backend
        router.put(route('calendar.update', event.id), {
            start: event.startStr,
            end: event.endStr,
            is_all_day: event.allDay,
        }, {
            preserveScroll: true,
        });
    } else {
        dropInfo.revert();
    }
}

function handleEventResize(resizeInfo) {
    const event = resizeInfo.event;
    if (event.extendedProps.isPersonal) {
        router.put(route('calendar.update', event.id), {
            start: event.startStr,
            end: event.endStr,
            is_all_day: event.allDay,
        }, {
            preserveScroll: true,
        });
    } else {
        resizeInfo.revert();
    }
}

function saveEvent() {
    if (isEditing.value) {
        form.put(route('calendar.update', form.id), {
            onSuccess: () => {
                showEventModal.value = false;
                form.reset();
            }
        });
    } else {
        form.post(route('calendar.store'), {
            onSuccess: () => {
                showEventModal.value = false;
                form.reset();
            }
        });
    }
}

function deleteEvent() {
    form.delete(route('calendar.destroy', form.id), {
        onSuccess: () => {
            showEventModal.value = false;
            showDeleteConfirm.value = false;
            form.reset();
        }
    });
}

function filterEvents(type) {
    activeFilter.value = type;
    if (type === 'all') {
        calendarOptions.value.events = props.events;
    } else {
        calendarOptions.value.events = props.events.filter(evt => {
            if (type === 'Subscription') {
                return evt.extendedProps.type === 'Subscription' || evt.extendedProps.type === 'Subscription Renewal';
            }
            return evt.extendedProps.type === type;
        });
    }
}

watch(() => props.events, (newEvents) => {
    filterEvents(activeFilter.value);
});

// Format date utility
const formatDateDisplay = (date) => {
    if (!date) return '';
    return date.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
};

const formatTimeDisplay = (date) => {
    if (!date) return '';
    return date.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
};

const formatMoney = (amount) => {
    return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(amount / 100);
};
</script>

<template>
    <AppLayout title="Mi Calendario">
        <main class="flex-1 p-6 bg-gray-100 font-sans">
            <div class="max-w-7xl mx-auto">
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">📅 Mi Calendario</h2>
                    <p class="text-gray-600">Todos tus eventos, tareas, reuniones y responsabilidades en un solo lugar</p>
                </div>

                <!-- Filters -->
                <div class="mb-6 bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Filtrar por tipo:</h3>
                    <div class="flex flex-wrap gap-2">
                        <button @click="filterEvents('all')" :class="{'active ring-2 ring-offset-1 ring-blue-500': activeFilter === 'all'}" class="filter-btn bg-white border border-gray-200 px-3 py-1 rounded-full text-sm hover:bg-gray-50 flex items-center transition-all">
                            <span class="inline-block w-3 h-3 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 mr-2"></span>
                            Todos
                        </button>
                        <button @click="filterEvents('Task')" :class="{'active ring-2 ring-offset-1 ring-blue-500': activeFilter === 'Task'}" class="filter-btn bg-white border border-gray-200 px-3 py-1 rounded-full text-sm hover:bg-gray-50 flex items-center transition-all">
                            <span class="inline-block w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                            📋 Tareas
                        </button>
                        <button @click="filterEvents('Event')" :class="{'active ring-2 ring-offset-1 ring-blue-500': activeFilter === 'Event'}" class="filter-btn bg-white border border-gray-200 px-3 py-1 rounded-full text-sm hover:bg-gray-50 flex items-center transition-all">
                            <span class="inline-block w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                            🎉 Eventos
                        </button>
                        <button @click="filterEvents('Meeting')" :class="{'active ring-2 ring-offset-1 ring-blue-500': activeFilter === 'Meeting'}" class="filter-btn bg-white border border-gray-200 px-3 py-1 rounded-full text-sm hover:bg-gray-50 flex items-center transition-all">
                            <span class="inline-block w-3 h-3 rounded-full bg-purple-500 mr-2"></span>
                            💼 Reuniones
                        </button>
                         <button @click="filterEvents('Project')" :class="{'active ring-2 ring-offset-1 ring-blue-500': activeFilter === 'Project'}" class="filter-btn bg-white border border-gray-200 px-3 py-1 rounded-full text-sm hover:bg-gray-50 flex items-center transition-all">
                            <span class="inline-block w-3 h-3 rounded-full bg-amber-500 mr-2"></span>
                            🚀 Proyectos
                        </button>
                        <button @click="filterEvents('Campaign')" :class="{'active ring-2 ring-offset-1 ring-blue-500': activeFilter === 'Campaign'}" class="filter-btn bg-white border border-gray-200 px-3 py-1 rounded-full text-sm hover:bg-gray-50 flex items-center transition-all">
                            <span class="inline-block w-3 h-3 rounded-full bg-pink-500 mr-2"></span>
                            📢 Campañas
                        </button>
                        <button @click="filterEvents('Subscription')" :class="{'active ring-2 ring-offset-1 ring-blue-500': activeFilter === 'Subscription'}" class="filter-btn bg-white border border-gray-200 px-3 py-1 rounded-full text-sm hover:bg-gray-50 flex items-center transition-all">
                            <span class="inline-block w-3 h-3 rounded-full bg-cyan-500 mr-2"></span>
                            💳 Suscripciones
                        </button>
                        <button @click="filterEvents('Case')" :class="{'active ring-2 ring-offset-1 ring-blue-500': activeFilter === 'Case'}" class="filter-btn bg-white border border-gray-200 px-3 py-1 rounded-full text-sm hover:bg-gray-50 flex items-center transition-all">
                            <span class="inline-block w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                            📁 Casos
                        </button>
                         <button @click="filterEvents('Invoice')" :class="{'active ring-2 ring-offset-1 ring-blue-500': activeFilter === 'Invoice'}" class="filter-btn bg-white border border-gray-200 px-3 py-1 rounded-full text-sm hover:bg-gray-50 flex items-center transition-all">
                            <span class="inline-block w-3 h-3 rounded-full bg-teal-500 mr-2"></span>
                            🧾 Facturas
                        </button>
                         <button @click="filterEvents('Certificate')" :class="{'active ring-2 ring-offset-1 ring-blue-500': activeFilter === 'Certificate'}" class="filter-btn bg-white border border-gray-200 px-3 py-1 rounded-full text-sm hover:bg-gray-50 flex items-center transition-all">
                            <span class="inline-block w-3 h-3 rounded-full bg-purple-500 mr-2"></span>
                            📜 Certificados
                        </button>
                         <button @click="filterEvents('Induction')" :class="{'active ring-2 ring-offset-1 ring-blue-500': activeFilter === 'Induction'}" class="filter-btn bg-white border border-gray-200 px-3 py-1 rounded-full text-sm hover:bg-gray-50 flex items-center transition-all">
                            <span class="inline-block w-3 h-3 rounded-full bg-indigo-600 mr-2"></span>
                            👥 Inducciones
                        </button>
                        <button @click="filterEvents('Policy')" :class="{'active ring-2 ring-offset-1 ring-blue-500': activeFilter === 'Policy'}" class="filter-btn bg-white border border-gray-200 px-3 py-1 rounded-full text-sm hover:bg-gray-50 flex items-center transition-all">
                            <span class="inline-block w-3 h-3 rounded-full bg-lime-500 mr-2"></span>
                            📋 Políticas
                        </button>
                         <button @click="filterEvents('Social Media Post')" :class="{'active ring-2 ring-offset-1 ring-blue-500': activeFilter === 'Social Media Post'}" class="filter-btn bg-white border border-gray-200 px-3 py-1 rounded-full text-sm hover:bg-gray-50 flex items-center transition-all">
                            <span class="inline-block w-3 h-3 rounded-full bg-cyan-400 mr-2"></span>
                            📱 Redes Sociales
                        </button>
                         <button @click="filterEvents('Punch Item')" :class="{'active ring-2 ring-offset-1 ring-blue-500': activeFilter === 'Punch Item'}" class="filter-btn bg-white border border-gray-200 px-3 py-1 rounded-full text-sm hover:bg-gray-50 flex items-center transition-all">
                            <span class="inline-block w-3 h-3 rounded-full bg-yellow-500 mr-2"></span>
                            🔧 Punch List
                        </button>
                         <button @click="filterEvents('Marketing Case')" :class="{'active ring-2 ring-offset-1 ring-blue-500': activeFilter === 'Marketing Case'}" class="filter-btn bg-white border border-gray-200 px-3 py-1 rounded-full text-sm hover:bg-gray-50 flex items-center transition-all">
                            <span class="inline-block w-3 h-3 rounded-full bg-rose-400 mr-2"></span>
                            📊 Casos Marketing
                        </button>
                    </div>
                </div>

                <!-- Calendar -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-lg">
                     <FullCalendar :options="calendarOptions" />
                </div>
            </div>
            
            <!-- Create/Edit Modal -->
            <DialogModal :show="showEventModal" @close="showEventModal = false">
                <template #title>
                    {{ isEditing ? 'Editar Evento' : 'Nuevo Evento' }}
                </template>
                <template #content>
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="title" value="Título" />
                            <TextInput id="title" v-model="form.title" type="text" class="mt-1 block w-full"  />
                            <InputError :message="form.errors.title" class="mt-2" />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="start" value="Inicio" />
                                <TextInput id="start" v-model="form.start" type="datetime-local" class="mt-1 block w-full" />
                                <InputError :message="form.errors.start" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="end" value="Fin" />
                                <TextInput id="end" v-model="form.end" type="datetime-local" class="mt-1 block w-full" />
                                <InputError :message="form.errors.end" class="mt-2" />
                            </div>
                        </div>
                         <div>
                            <label class="flex items-center">
                                <input type="checkbox" v-model="form.is_all_day" class="form-checkbox h-4 w-4 text-blue-600 transition duration-150 ease-in-out bg-gray-50 border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">Todo el día</span>
                            </label>
                        </div>
                        <div>
                            <InputLabel for="description" value="Descripción" />
                            <textarea id="description" v-model="form.description" rows="3" class="mt-1 block w-full border-gray-300 focus:border-yellow-500 focus:ring-yellow-500 rounded-md shadow-sm"></textarea>
                        </div>
                        <div>
                             <InputLabel value="Color" />
                             <div class="mt-1 flex gap-2">
                                <button v-for="c in ['#3b82f6', '#10b981', '#ef4444', '#f59e0b', '#8b5cf6', '#ec4899']" :key="c" type="button" @click="form.color = c"
                                    class="w-6 h-6 rounded-full focus:outline-none ring-2 ring-offset-2 ring-offset-white"
                                    :class="form.color === c ? 'ring-gray-400' : 'ring-transparent'"
                                    :style="{ backgroundColor: c }">
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <SecondaryButton @click="showEventModal = false">
                        Cancelar
                    </SecondaryButton>
                    
                    <DangerButton v-if="isEditing" @click="showDeleteConfirm = true" class="ml-3">
                        Eliminar
                    </DangerButton>

                    <PrimaryButton @click="saveEvent" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="ml-3 bg-blue-600 hover:bg-blue-700">
                         {{ isEditing ? 'Actualizar' : 'Guardar' }}
                    </PrimaryButton>
                </template>
            </DialogModal>

            <!-- Delete Confirmation Modal -->
            <DialogModal :show="showDeleteConfirm" @close="showDeleteConfirm = false">
                <template #title>
                    Eliminar Evento
                </template>
                <template #content>
                    ¿Estás seguro de que deseas eliminar este evento? Esta acción no se puede deshacer.
                </template>
                <template #footer>
                    <SecondaryButton @click="showDeleteConfirm = false">
                        Cancelar
                    </SecondaryButton>
                    <DangerButton @click="deleteEvent" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="ml-3">
                        Eliminar
                    </DangerButton>
                </template>
            </DialogModal>

            <!-- Details Modal (Using DialogModal for consistency) -->
            <DialogModal :show="showDetailsModal" @close="showDetailsModal = false">
                <template #title>
                    {{ selectedEventDetails?.title }}
                </template>
                <template #content>
                    <div v-if="selectedEventDetails" class="space-y-3 text-sm text-gray-600">
                        <p>
                            <strong class="text-gray-900">📅 Fecha:</strong>
                            {{ formatDateDisplay(selectedEventDetails.start) }}
                        </p>
                        <p v-if="!selectedEventDetails.allDay && selectedEventDetails.start">
                            <strong class="text-gray-900">⏰ Hora:</strong>
                            {{ formatTimeDisplay(selectedEventDetails.start) }}
                            <span v-if="selectedEventDetails.end"> - {{ formatTimeDisplay(selectedEventDetails.end) }}</span>
                        </p>
                        <p>
                            <strong class="text-gray-900">🏷️ Tipo:</strong>
                            {{ selectedEventDetails.type }}
                        </p>
                         <p>
                            <strong class="text-gray-900">📊 Estado:</strong>
                            <span :style="{ backgroundColor: selectedEventDetails.color }" class="px-2 py-1 rounded text-white text-xs">
                                {{ selectedEventDetails.status }}
                            </span>
                        </p>
                        <!-- Dynamic details based on type -->
                        <p v-if="selectedEventDetails.description">
                            <strong class="text-gray-900">📝 Descripción:</strong>
                            {{ selectedEventDetails.description }}
                        </p>
                        <p v-if="selectedEventDetails.location">
                            <strong class="text-gray-900">📍 Ubicación:</strong>
                            {{ selectedEventDetails.location }}
                        </p>
                        <p v-if="selectedEventDetails.goal">
                            <strong class="text-gray-900">🎯 Objetivo:</strong>
                            {{ selectedEventDetails.goal }}
                        </p>
                        <p v-if="selectedEventDetails.url">
                            <strong class="text-gray-900">🔗 URL:</strong>
                            <a :href="selectedEventDetails.url" target="_blank" class="text-blue-600 underline">{{ selectedEventDetails.url }}</a>
                        </p>
                        <p v-if="selectedEventDetails.address">
                            <strong class="text-gray-900">📍 Dirección:</strong>
                            {{ selectedEventDetails.address }}
                        </p>
                        <p v-if="selectedEventDetails.amount">
                            <strong class="text-gray-900">💰 Monto:</strong>
                            {{ formatMoney(selectedEventDetails.amount) }}
                        </p>
                         <p v-if="selectedEventDetails.total">
                            <strong class="text-gray-900">💵 Total:</strong>
                            {{ formatMoney(selectedEventDetails.total) }}
                        </p>
                        <p v-if="selectedEventDetails.contact">
                            <strong class="text-gray-900">👤 Contacto:</strong>
                            {{ selectedEventDetails.contact }}
                        </p>
                        <p v-if="selectedEventDetails.caseType">
                            <strong class="text-gray-900">📋 Tipo de Caso:</strong>
                            {{ selectedEventDetails.caseType }}
                        </p>
                         <p v-if="selectedEventDetails.project">
                            <strong class="text-gray-900">🚀 Proyecto:</strong>
                            {{ selectedEventDetails.project }}
                        </p>
                         <p v-if="selectedEventDetails.worksite">
                            <strong class="text-gray-900">🏗️ Obra:</strong>
                            {{ selectedEventDetails.worksite }}
                        </p>
                         <p v-if="selectedEventDetails.mediums">
                            <strong class="text-gray-900">📱 Medios:</strong>
                            {{ selectedEventDetails.mediums }}
                        </p>
                        <p v-if="selectedEventDetails.employee">
                            <strong class="text-gray-900">👥 Empleado:</strong>
                            {{ selectedEventDetails.employee }}
                        </p>

                    </div>
                </template>
                <template #footer>
                     <SecondaryButton @click="showDetailsModal = false">
                        Cerrar
                    </SecondaryButton>
                </template>
            </DialogModal>

        </main>
    </AppLayout>
</template>

<style>
/* FullCalendar overrides to match previous design */
.fc {
    font-family: inherit;
}
.fc-theme-standard td, .fc-theme-standard th {
    border-color: #e5e7eb;
}
.fc .fc-toolbar-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
}
.fc .fc-button-primary {
    background-color: #3b82f6;
    border-color: #3b82f6;
}
.fc .fc-button-primary:hover {
    background-color: #2563eb;
    border-color: #2563eb;
}
.fc-day-today {
    background-color: rgba(59, 130, 246, 0.05) !important;
}
.fc-event {
    cursor: pointer;
    border: none;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
</style>
