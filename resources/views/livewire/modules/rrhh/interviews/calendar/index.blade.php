<div>
    <div class="p-6 max-w-7xl mx-auto">
        <div class="mb-6">
            <h2 class="text-3xl font-bold text-black mb-2">🎤 Calendario de Entrevistas</h2>
            <p class="text-gray-400">Todas las entrevistas del sistema y sus entrevistadores asignados</p>
        </div>

        <!-- Leyenda de Estados -->
        <div class="mb-6 bg-[#1f1f1f] rounded-xl border border-gray-700 p-4">
            <h3 class="text-sm font-semibold text-gray-300 mb-3">Estados:</h3>
            <div class="flex flex-wrap gap-3">
                <div class="flex items-center">
                    <span class="inline-block w-4 h-4 rounded bg-blue-500 mr-2"></span>
                    <span class="text-sm text-gray-300">Programada</span>
                </div>
                <div class="flex items-center">
                    <span class="inline-block w-4 h-4 rounded bg-amber-500 mr-2"></span>
                    <span class="text-sm text-gray-300">En Proceso</span>
                </div>
                <div class="flex items-center">
                    <span class="inline-block w-4 h-4 rounded bg-green-500 mr-2"></span>
                    <span class="text-sm text-gray-300">Completada</span>
                </div>
                <div class="flex items-center">
                    <span class="inline-block w-4 h-4 rounded bg-red-500 mr-2"></span>
                    <span class="text-sm text-gray-300">Cancelada</span>
                </div>
            </div>
        </div>

        <!-- Calendario -->
        <div class="bg-[#1f1f1f] rounded-xl border border-gray-700 p-6 shadow-2xl">
            <div id="interview-calendar"></div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* Estilos del calendario de entrevistas */
        #interview-calendar {
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
        const calendarEl = document.getElementById('interview-calendar');
        const allEvents = @json($events);

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
            displayEventEnd: false,
            height: 'auto',
            contentHeight: 650,

            // Mostrar detalles de la entrevista al hacer clic
            eventClick: function (info) {
                const props = info.event.extendedProps;
                let detailsHTML = `
                    <div style="background: #1f1f1f; border: 1px solid #4b5563; border-radius: 8px; padding: 1.5rem; max-width: 500px;">
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
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #d1d5db;">⏰ Hora:</strong> 
                                ${info.event.start.toLocaleTimeString('es-ES', {
                    hour: '2-digit',
                    minute: '2-digit'
                })}
                            </p>
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #d1d5db;">👤 Candidato:</strong> 
                                ${props.applicant}
                            </p>
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #d1d5db;">👨‍💼 Entrevistador:</strong> 
                                ${props.interviewer}
                            </p>
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #d1d5db;">📋 Tipo:</strong> 
                                ${props.interviewType}
                            </p>
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #d1d5db;">📊 Estado:</strong> 
                                <span style="background: ${info.event.backgroundColor}; padding: 0.25rem 0.5rem; border-radius: 0.25rem; color: #fff;">
                                    ${props.status}
                                </span>
                            </p>
                            <p style="margin: 0.5rem 0;">
                                <strong style="color: #d1d5db;">✅ Resultado:</strong> 
                                ${props.result}
                            </p>
                `;

                if (props.rating > 0) {
                    detailsHTML += `
                        <p style="margin: 0.5rem 0;">
                            <strong style="color: #d1d5db;">⭐ Calificación:</strong> 
                            ${props.rating}/10
                        </p>
                    `;
                }

                if (props.platform) {
                    detailsHTML += `
                        <p style="margin: 0.5rem 0;">
                            <strong style="color: #d1d5db;">💻 Plataforma:</strong> 
                            ${props.platform}
                        </p>
                    `;
                }

                if (props.platform_url) {
                    detailsHTML += `
                        <p style="margin: 0.5rem 0;">
                            <strong style="color: #d1d5db;">🔗 Link:</strong> 
                            <a href="${props.platform_url}" target="_blank" style="color: #3b82f6; text-decoration: underline;">
                                Unirse a la reunión
                            </a>
                        </p>
                    `;
                }

                if (props.expected_results) {
                    detailsHTML += `
                        <p style="margin: 0.5rem 0;">
                            <strong style="color: #d1d5db;">🎯 Resultados Esperados:</strong> 
                            ${props.expected_results}
                        </p>
                    `;
                }

                if (props.observations) {
                    detailsHTML += `
                        <p style="margin: 0.5rem 0;">
                            <strong style="color: #d1d5db;">📝 Observaciones:</strong> 
                            ${props.observations}
                        </p>
                    `;
                }

                detailsHTML += `
                        </div>
                    </div>
                `;

                // Crear y mostrar el modal de detalles
                const existingModal = document.getElementById('interview-details-modal');
                if (existingModal) {
                    existingModal.remove();
                }

                const modal = document.createElement('div');
                modal.id = 'interview-details-modal';
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
                        <button onclick="this.closest('#interview-details-modal').remove()" 
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
    });
</script>