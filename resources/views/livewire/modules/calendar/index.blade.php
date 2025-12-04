<div>
    <div class="p-6 max-w-7xl mx-auto">
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-black mb-2">📅 Mi Calendario</h2>
            <p class="text-gray-400">Todos tus eventos, tareas, reuniones y responsabilidades en un solo lugar</p>
        </div>

        <!-- Filtros de Tipo de Evento -->
        <div class="mb-6 bg-[#1f1f1f] rounded-xl border border-gray-700 p-4">
            <h3 class="text-sm font-semibold text-gray-300 mb-3">Filtrar por tipo:</h3>
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
        <div class="bg-[#1f1f1f] rounded-xl border border-gray-700 p-6 shadow-2xl">
            <div id="calendar"></div>
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
            background-color: #2d2d2d;
            border: 1px solid #3d3d3d;
            color: #9ca3af;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-btn:hover {
            background-color: #3d3d3d;
            border-color: #4d4d4d;
            color: #fff;
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
            border-color: #374151 !important;
        }

        .fc-theme-standard .fc-scrollgrid {
            border-color: #374151 !important;
        }

        .fc .fc-toolbar-title {
            color: #fff !important;
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
            background-color: #4b5563 !important;
            border-color: #4b5563 !important;
            opacity: 0.5;
        }

        .fc .fc-button-active {
            background-color: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
        }

        .fc-day-today {
            background-color: rgba(59, 130, 246, 0.1) !important;
        }

        .fc .fc-col-header-cell {
            background-color: #2d2d2d !important;
            color: #9ca3af !important;
            font-weight: 600 !important;
            padding: 0.75rem 0 !important;
            text-transform: uppercase !important;
            font-size: 0.75rem !important;
            letter-spacing: 0.05em !important;
        }

        .fc .fc-daygrid-day-number {
            color: #d1d5db !important;
            font-weight: 500 !important;
            padding: 0.5rem !important;
        }

        .fc .fc-daygrid-day {
            background-color: #1a1a1a !important;
        }

        .fc .fc-daygrid-day:hover {
            background-color: #252525 !important;
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .fc-event-title {
            font-weight: 500 !important;
        }

        /* Popup de detalles del evento */
        .fc-popover {
            background-color: #2d2d2d !important;
            border: 1px solid #4b5563 !important;
            border-radius: 0.5rem !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5) !important;
        }

        .fc-popover-header {
            background-color: #3b82f6 !important;
            color: #fff !important;
            padding: 0.75rem !important;
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }

        .fc-popover-body {
            color: #d1d5db !important;
            padding: 0.75rem !important;
        }

        /* Vista de lista */
        .fc-list-event:hover td {
            background-color: #2d2d2d !important;
        }

        .fc-list-day-cushion {
            background-color: #2d2d2d !important;
            color: #fff !important;
        }

        .fc-list-event-time,
        .fc-list-event-title {
            color: #d1d5db !important;
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

            // Mostrar detalles del evento al hacer clic
            eventClick: function (info) {
                const props = info.event.extendedProps;
                let detailsHTML = `
                        <div style="background: #1f1f1f; border: 1px solid #4b5563; border-radius: 8px; padding: 1rem; max-width: 400px;">
                            <h3 style="color: #fff; font-size: 1.25rem; font-weight: 700; margin-bottom: 0.75rem;">
                                ${info.event.title}
                            </h3>
                            <div style="color: #9ca3af; font-size: 0.875rem; line-height: 1.6;">
                                <p style="margin: 0.5rem 0;">
                                    <strong style="color: #d1d5db;">📅 Fecha:</strong> 
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
                                <strong style="color: #d1d5db;">⏰ Hora:</strong> 
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
                            <strong style="color: #d1d5db;">🏷️ Tipo:</strong> 
                            ${props.type}
                        </p>
                        <p style="margin: 0.5rem 0;">
                            <strong style="color: #d1d5db;">📊 Estado:</strong> 
                            <span style="background: ${info.event.backgroundColor}; padding: 0.25rem 0.5rem; border-radius: 0.25rem; color: #fff;">
                                ${props.status}
                            </span>
                        </p>
                    `;

                // Agregar detalles específicos según el tipo
                if (props.description) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #d1d5db;">📝 Descripción:</strong> 
                                ${props.description}
                            </p>
                        `;
                }

                if (props.location) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #d1d5db;">📍 Ubicación:</strong> 
                                ${props.location}
                            </p>
                        `;
                }

                if (props.goal) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #d1d5db;">🎯 Objetivo:</strong> 
                                ${props.goal}
                            </p>
                        `;
                }

                if (props.url) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #d1d5db;">🔗 URL:</strong> 
                                <a href="${props.url}" target="_blank" style="color: #3b82f6; text-decoration: underline;">
                                    ${props.url}
                                </a>
                            </p>
                        `;
                }

                if (props.address) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #d1d5db;">📍 Dirección:</strong> 
                                ${props.address}
                            </p>
                        `;
                }

                if (props.amount) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #d1d5db;">💰 Monto:</strong> 
                                $${props.amount.toLocaleString('es-ES')}
                            </p>
                        `;
                }

                if (props.total) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #d1d5db;">💵 Total:</strong> 
                                $${props.total.toLocaleString('es-ES')}
                            </p>
                        `;
                }

                if (props.contact) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #d1d5db;">👤 Contacto:</strong> 
                                ${props.contact}
                            </p>
                        `;
                }

                if (props.caseType) {
                    detailsHTML += `
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #d1d5db;">📋 Tipo de Caso:</strong> 
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