<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="w-[90%] bg-white border-b shadow-xs h-12 flex items-center relative rounded-full m-4">
        <div id="scrollContainer" class="flex-1 overflow-x-auto whitespace-nowrap flex items-center scrollbar-hide scroll-smooth">
            <ul class="flex items-center space-x-6 text-sm font-medium px-4 min-w-fit mx-auto">
                <li>
                    <Link :href="route('rrhh.contracts.index')"
                          :class="[route().current('rrhh.contracts.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600', 'ml-7']">
                        Contratos
                    </Link>
                </li>
                <li>
                    <Link :href="route('rrhh.employees.index')"
                          :class="route().current('rrhh.employees.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600'">
                        Empleados
                    </Link>
                </li>
                <li>
                    <Link :href="route('rrhh.vacancies.index')"
                          :class="route().current('rrhh.vacancies.*') || route().current('rrhh.applicants.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600'">
                        Reclutamiento
                    </Link>
                </li>
                <li>
                    <Link :href="route('rrhh.interviews.index')"
                          :class="route().current('rrhh.interviews.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600'">
                        Entrevistas
                    </Link>
                </li>
                <li>
                    <Link :href="route('rrhh.interviews.calendar')"
                          :class="route().current('rrhh.interviews.calendar') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600'">
                        Calendario Entrevistas
                    </Link>
                </li>
                <li>
                    <Link :href="route('rrhh.inductions.index')"
                          :class="route().current('rrhh.inductions.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600'">
                        Inducciones
                    </Link>
                </li>
                <li>
                    <Link :href="route('rrhh.kits.index')"
                          :class="route().current('rrhh.kits.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600'">
                        Kits Bienvenida
                    </Link>
                </li>
                <!-- <li>
                    <a href="#" class="hover:text-purple-600">Evaluaciones</a>
                </li> -->
                <li>
                    <Link :href="route('rrhh.birthdays.index')"
                          :class="route().current('rrhh.birthdays.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600'">
                        Cumpleaños
                    </Link>
                </li>
                <li>
                    <Link :href="route('rrhh.offboardings.index')"
                          :class="route().current('rrhh.offboardings.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600'">
                        Off-boarding
                    </Link>
                </li>
                <li>
                    <Link :href="route('rrhh.holidays.index')"
                          :class="route().current('rrhh.holidays.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600'">
                        Vacaciones/Permisos med.
                    </Link>
                </li>
                <li>
                    <Link :href="route('rrhh.attendances.index')"
                          :class="route().current('rrhh.attendances.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600'">
                        Asistencia
                    </Link>
                </li>
            </ul>
        </div>
    </div>
</template>
