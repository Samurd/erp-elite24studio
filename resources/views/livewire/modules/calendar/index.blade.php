<div>
    <div class="p-6 max-w-7xl mx-auto">
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">📅 Mi Calendario</h2>
            <p class="text-gray-600">Todos tus eventos, tareas, reuniones y responsabilidades en un solo lugar</p>
        </div>

        <!-- Filtros de Tipo de Evento -->
        <div class="mb-6 bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Filtrar por tipo:</h3>
            <div class="flex flex-wrap gap-2" id="event-filters">
                <button class="filter-btn active" data-type="all">
                    <span
                        class="inline-block w-3 h-3 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 mr-2"></span>
                    Todos
                </button>
                <button class="filter-btn" data-type="Task">
                    <span class="inline-block w-3 h-3 rounded-full bg-blue-500 mr-2"></span>
                    📋 Tareas
                </button>
                <button class="filter-btn" data-type="Event">
                    <span class="inline-block w-3 h-3 rounded-full bg-green-500 mr-2"></span>
                    🎉 Eventos
                </button>
                <button class="filter-btn" data-type="Meeting">
                    <span class="inline-block w-3 h-3 rounded-full bg-purple-500 mr-2"></span>
                    💼 Reuniones
                </button>
                <button class="filter-btn" data-type="Project">
                    <span class="inline-block w-3 h-3 rounded-full bg-amber-500 mr-2"></span>
                    🚀 Proyectos
                </button>
                <button class="filter-btn" data-type="Campaign">
                    <span class="inline-block w-3 h-3 rounded-full bg-pink-500 mr-2"></span>
                    📢 Campañas
                </button>
                <button class="filter-btn" data-type="Subscription">
                    <span class="inline-block w-3 h-3 rounded-full bg-cyan-500 mr-2"></span>
                    💳 Suscripciones
                </button>
                <button class="filter-btn" data-type="Case">
                    <span class="inline-block w-3 h-3 rounded-full bg-red-500 mr-2"></span>
                    📁 Casos
                </button>
                <button class="filter-btn" data-type="Invoice">
                    <span class="inline-block w-3 h-3 rounded-full bg-teal-500 mr-2"></span>
                    🧾 Facturas
                </button>
                <button class="filter-btn" data-type="Certificate">
                    <span class="inline-block w-3 h-3 rounded-full bg-purple-500 mr-2"></span>
                    📜 Certificados
                </button>
                <button class="filter-btn" data-type="Induction">
                    <span class="inline-block w-3 h-3 rounded-full bg-indigo-600 mr-2"></span>
                    👥 Inducciones
                </button>
                <button class="filter-btn" data-type="Policy">
                    <span class="inline-block w-3 h-3 rounded-full bg-lime-500 mr-2"></span>
                    📋 Políticas
                </button>
                <button class="filter-btn" data-type="Social Media Post">
                    <span class="inline-block w-3 h-3 rounded-full bg-cyan-400 mr-2"></span>
                    📱 Redes Sociales
                </button>
                <button class="filter-btn" data-type="Punch Item">
                    <span class="inline-block w-3 h-3 rounded-full bg-yellow-500 mr-2"></span>
                    🔧 Punch List
                </button>
                <button class="filter-btn" data-type="Marketing Case">
                    <span class="inline-block w-3 h-3 rounded-full bg-rose-400 mr-2"></span>
                    📊 Casos Marketing
                </button>
            </div>
        </div>

        <!-- Calendario -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-lg">
            <div id="calendar" wire:ignore></div>
        </div>
    </div>

    <!-- Event Modal -->
    <div x-data="{ open: false }" x-on:open-modal.window="open = true" x-on:close-modal.window="open = false"
        x-on:event-saved.window="open = false" x-show="open" style="display: none;"
        class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay, show/hide based on modal state. -->
            <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                @click="open = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-200"
                @click.stop>
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title"
                                x-text="$wire.eventId ? 'Editar Evento' : 'Nuevo Evento'">
                                Nuevo Evento
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Título</label>
                                    <input type="text" wire:model="title"
                                        class="mt-1 block w-full bg-gray-50 border-gray-300 rounded-md text-gray-900 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Inicio</label>
                                        <input type="datetime-local" wire:model="start"
                                            class="mt-1 block w-full bg-gray-50 border-gray-300 rounded-md text-gray-900 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Fin</label>
                                        <input type="datetime-local" wire:model="end"
                                            class="mt-1 block w-full bg-gray-50 border-gray-300 rounded-md text-gray-900 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="isAllDay"
                                            class="form-checkbox h-4 w-4 text-blue-600 transition duration-150 ease-in-out bg-gray-50 border-gray-300">
                                        <span class="ml-2 text-sm text-gray-700">Todo el día</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                                    <textarea wire:model="description" rows="3"
                                        class="mt-1 block w-full bg-gray-50 border-gray-300 rounded-md text-gray-900 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Color</label>
                                    <div class="mt-1 flex gap-2">
                                        @foreach(['#3b82f6', '#10b981', '#ef4444', '#f59e0b', '#8b5cf6', '#ec4899'] as $c)
                                            <button type="button" wire:click="$set('color', '{{ $c }}')"
                                                class="w-6 h-6 rounded-full focus:outline-none ring-2 ring-offset-2 ring-offset-white"
                                                :class="$wire.color === '{{ $c }}' ? 'ring-gray-400' : 'ring-transparent'"
                                                style="background-color: {{ $c }};">
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                    <button type="button" wire:click="saveEvent"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Guardar
                    </button>
                    <button x-show="$wire.eventId" type="button" wire:click="deleteEvent($wire.eventId); open = false;"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Eliminar
                    </button>
                    <button type="button" @click="open = false"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* Estilos personalizados para los filtros */
        .filter-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            color: #4b5563;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-btn:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
            color: #111827;
            transform: translateY(-1px);
        }

        .filter-btn.active {
            background-color: #3b82f6;
            border-color: #3b82f6;
            color: #fff;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.3);
        }

        /* Estilos del calendario */
        #calendar {
            background: transparent;
        }

        .fc {
            background: transparent;
        }

        .fc-theme-standard td,
        .fc-theme-standard th {
            border-color: #e5e7eb !important;
        }

        .fc-theme-standard .fc-scrollgrid {
            border-color: #e5e7eb !important;
        }

        .fc .fc-toolbar-title {
            color: #111827 !important;
            font-size: 1.5rem !important;
            font-weight: 700 !important;
        }

        .fc .fc-button {
            background-color: #3b82f6 !important;
            border-color: #3b82f6 !important;
            color: #fff !important;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem !important;
            font-weight: 500 !important;
            transition: all 0.2s !important;
        }

        .fc .fc-button:hover {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .fc .fc-button:disabled {
            background-color: #9ca3af !important;
            border-color: #9ca3af !important;
            opacity: 0.5;
        }

        .fc .fc-button-active {
            background-color: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
        }

        .fc-day-today {
            background-color: rgba(59, 130, 246, 0.05) !important;
        }

        .fc .fc-col-header-cell {
            background-color: #f9fafb !important;
            color: #4b5563 !important;
            font-weight: 600 !important;
            padding: 0.75rem 0 !important;
            text-transform: uppercase !important;
            font-size: 0.75rem !important;
            letter-spacing: 0.05em !important;
        }

        .fc .fc-daygrid-day-number {
            color: #6b7280 !important;
            font-weight: 500 !important;
            padding: 0.5rem !important;
        }

        .fc .fc-daygrid-day {
            background-color: #ffffff !important;
        }

        .fc .fc-daygrid-day:hover {
            background-color: #f9fafb !important;
        }

        .fc-event {
            border: none !important;
            padding: 2px 4px !important;
            margin: 1px 2px !important;
            border-radius: 4px !important;
            font-size: 0.75rem !important;
            font-weight: 500 !important;
            cursor: pointer !important;
            transition: all 0.2s !important;
        }

        .fc-event:hover {
            transform: scale(1.02);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .fc-event-title {
            font-weight: 500 !important;
        }

        /* Popup de detalles del evento */
        .fc-popover {
            background-color: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .fc-popover-header {
            background-color: #3b82f6 !important;
            color: #fff !important;
            padding: 0.75rem !important;
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }

        .fc-popover-body {
            color: #374151 !important;
            padding: 0.75rem !important;
        }

        /* Vista de lista */
        .fc-list-event:hover td {
            background-color: #f9fafb !important;
        }

        .fc-list-day-cushion {
            background-color: #f3f4f6 !important;
            color: #111827 !important;
        }

        .fc-list-event-time,
        .fc-list-event-title {
            color: #374151 !important;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');
        const allEvents = @json($events);
        let activeFilter = 'all';

        // Inicializar calendario
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
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
            events: allEvents,
            eventDisplay: 'block',
            displayEventTime: true,
            displayEventEnd: true,
            height: 'auto',
            contentHeight: 650,

            selectable: true,
            editable: true, // Allow editing generally, control per event via properties

            // Create new event
            select: function (info) {
                @this.set('title', '');
                @this.set('description', '');
                @this.set('start', info.startStr.slice(0, 16)); // Format for datetime-local
                @this.set('end', info.endStr ? info.endStr.slice(0, 16) : info.startStr.slice(0, 16));
                @this.set('isAllDay', info.allDay);
                @this.set('eventId', null);
                @this.set('color', '#3b82f6');

                window.dispatchEvent(new CustomEvent('open-modal'));
            },

            // Drag and drop / Resize
            eventDrop: function (info) {
                if (info.event.extendedProps.isPersonal) {
                    // Optimistic update: Update local data immediately
                    const eventIndex = allEvents.findIndex(e => e.id === info.event.id);
                    if (eventIndex !== -1) {
                        allEvents[eventIndex].start = info.event.startStr;
                        allEvents[eventIndex].end = info.event.endStr;
                    }

                    @this.call('updateEventDrop',
                        info.event.extendedProps.eventId,
                        info.event.startStr,
                        info.event.endStr
                    );
                } else {
                    info.revert();
                }
            },

            eventResize: function (info) {
                if (info.event.extendedProps.isPersonal) {
                    // Optimistic update: Update local data immediately
                    const eventIndex = allEvents.findIndex(e => e.id === info.event.id);
                    if (eventIndex !== -1) {
                        allEvents[eventIndex].start = info.event.startStr;
                        allEvents[eventIndex].end = info.event.endStr;
                    }

                    @this.call('updateEventDrop',
                        info.event.extendedProps.eventId,
                        info.event.startStr,
                        info.event.endStr
                    );
                } else {
                    info.revert();
                }
            },

            // Click event (Edit or View Details)
            eventClick: function (info) {
                const props = info.event.extendedProps;

                // If it's a personal event, open edit modal
                if (props.isPersonal) {
                    @this.set('eventId', props.eventId);
                    @this.set('title', info.event.title);
                    @this.set('description', props.description);
                    // Format dates for input
                    let start = info.event.start;
                    let end = info.event.end || info.event.start;

                    // Adjust for timezone offset for input value
                    const offset = start.getTimezoneOffset() * 60000;
                    const startLocal = new Date(start.getTime() - offset);
                    const endLocal = new Date(end.getTime() - offset);

                    @this.set('start', startLocal.toISOString().slice(0, 16));
                    @this.set('end', endLocal.toISOString().slice(0, 16));

                    @this.set('isAllDay', info.event.allDay);
                    @this.set('color', info.event.backgroundColor);

                    window.dispatchEvent(new CustomEvent('open-modal'));
                    return;
                }

                // Otherwise show details popover
                let detailsHTML = `
                        <div style="background: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; padding: 1rem; max-width: 400px;">
                            <h3 style="color: #111827; font-size: 1.25rem; font-weight: 700; margin-bottom: 0.75rem;">
                                ${info.event.title}
                            </h3>
                            <div style="color: #4b5563; font-size: 0.875rem; line-height: 1.6;">
                                <p style="margin: 0.5rem 0;">
                                    <strong style="color: #111827;">📅 Fecha:</strong> 
                                    ${info.event.start.toLocaleDateString('es-ES', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                })}
                                </p>
                    `;

                if (!info.event.allDay && info.event.start) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #111827;">⏰ Hora:</strong> 
                                ${info.event.start.toLocaleTimeString('es-ES', {
                        hour: '2-digit',
                        minute: '2-digit'
                    })}
                                ${info.event.end ? ' - ' + info.event.end.toLocaleTimeString('es-ES', {
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : ''}
                            </p>
                        `;
                }

                detailsHTML += `
                        <p style="margin: 0.5rem 0;">
                            <strong style="color: #111827;">🏷️ Tipo:</strong> 
                            ${props.type}
                        </p>
                        <p style="margin: 0.5rem 0;">
                            <strong style="color: #111827;">📊 Estado:</strong> 
                            <span style="background: ${info.event.backgroundColor}; padding: 0.25rem 0.5rem; border-radius: 0.25rem; color: #fff;">
                                ${props.status}
                            </span>
                        </p>
                    `;

                // Agregar detalles específicos según el tipo
                if (props.description) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #111827;">📝 Descripción:</strong> 
                                ${props.description}
                            </p>
                        `;
                }

                if (props.location) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #111827;">📍 Ubicación:</strong> 
                                ${props.location}
                            </p>
                        `;
                }

                if (props.goal) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #111827;">🎯 Objetivo:</strong> 
                                ${props.goal}
                            </p>
                        `;
                }

                if (props.url) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #111827;">🔗 URL:</strong> 
                                <a href="${props.url}" target="_blank" style="color: #3b82f6; text-decoration: underline;">
                                    ${props.url}
                                </a>
                            </p>
                        `;
                }

                if (props.address) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #111827;">📍 Dirección:</strong> 
                                ${props.address}
                            </p>
                        `;
                }

                if (props.amount) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #111827;">💰 Monto:</strong> 
                                $${props.amount.toLocaleString('es-ES')}
                            </p>
                        `;
                }

                if (props.total) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #111827;">💵 Total:</strong> 
                                $${props.total.toLocaleString('es-ES')}
                            </p>
                        `;
                }

                if (props.contact) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #111827;">👤 Contacto:</strong> 
                                ${props.contact}
                            </p>
                        `;
                }

                if (props.caseType) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #111827;">📋 Tipo de Caso:</strong> 
                                ${props.caseType}
                            </p>
                        `;
                }

                detailsHTML += `
                            </div>
                        </div>
                    `;

                // Crear y mostrar el modal de detalles
                const existingModal = document.getElementById('event-details-modal');
                if (existingModal) {
                    existingModal.remove();
                }

                const modal = document.createElement('div');
                modal.id = 'event-details-modal';
                modal.style.cssText = `
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.7);
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        z-index: 10000;
                        animation: fadeIn 0.2s;
                    `;
                modal.innerHTML = `
                        <div style="position: relative; animation: slideIn 0.3s;">
                            ${detailsHTML}
                            <button onclick="this.closest('#event-details-modal').remove()" 
                                style="position: absolute; top: -10px; right: -10px; background: #ef4444; color: #fff; 
                                       border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer; 
                                       font-weight: bold; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                                ✕
                            </button>
                        </div>
                    `;
                modal.onclick = function (e) {
                    if (e.target === modal) {
                        modal.remove();
                    }
                };
                document.body.appendChild(modal);
            }
        });

        calendar.render();

        // Listen for calendar refresh events from Livewire
        window.addEventListener('refresh-calendar', event => {
            const newEvents = event.detail.events;
            // Update the local variable so filters work correctly
            allEvents.splice(0, allEvents.length, ...newEvents);

            // Re-apply current filter
            const filterType = activeFilter;

            if (filterType === 'all') {
                calendar.removeAllEventSources();
                calendar.addEventSource(allEvents);
            } else {
                const filteredEvents = allEvents.filter(evt => {
                    if (filterType === 'Subscription') {
                        return evt.extendedProps.type === 'Subscription' ||
                            evt.extendedProps.type === 'Subscription Renewal';
                    }
                    return evt.extendedProps.type === filterType;
                });
                calendar.removeAllEventSources();
                calendar.addEventSource(filteredEvents);
            }
        });

        // Filtros de eventos
        const filterButtons = document.querySelectorAll('.filter-btn');
        filterButtons.forEach(button => {
            button.addEventListener('click', function () {
                // Actualizar botón activo
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                // Aplicar filtro
                const filterType = this.getAttribute('data-type');
                activeFilter = filterType;

                if (filterType === 'all') {
                    calendar.removeAllEventSources();
                    calendar.addEventSource(allEvents);
                } else {
                    const filteredEvents = allEvents.filter(event => {
                        // Para suscripciones que tienen "Subscription" y "Subscription Renewal"
                        if (filterType === 'Subscription') {
                            return event.extendedProps.type === 'Subscription' ||
                                event.extendedProps.type === 'Subscription Renewal';
                        }
                        return event.extendedProps.type === filterType;
                    });
                    calendar.removeAllEventSources();
                    calendar.addEventSource(filteredEvents);
                }
            });
        });
    });
</script>