<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const permissions = computed(() => page.props.auth.permissions);
const currentRoute = computed(() => window.location.pathname); // Or use route().current() if passing ziggy

// Helper to check active state
const isActive = (path) => {
    return currentRoute.value.startsWith(path);
};

// Assuming icons will be replaced by images or handled later. 
// For now using text or placeholders if icons aren't available as Vue components.
// The blade used <x-ri-dashboard-fill> etc. which are blade components.
// I can't directly use them here. I should use a library like heroicons or fontawesome if available,
// or SVG strings. For speed, I'll use simple SVGs or just text/placeholders for now if I don't have the SVGs.
// User wants design fidelity, so I should try to map them if possible.
// Given strict instructions to verify design, I will assume fontAwesome is loaded in app.blade.php layout (yes it is).
// So I can use <i class="fas ..."></i> or similar.
// Looking at blade: <x-ri-dashboard-fill>, <x-ionicon-stats-chart>. These seem like specific icon sets.
// Checking app.blade.php, I see NO fontawesome (except some specific ones).
// Wait, index.blade.php used <i class="fa-solid ..."></i>. So FontAwesome IS available.

</script>

<template>
    <aside class="fixed top-0 left-0 z-30 h-screen w-[280px] bg-[#252525] shadow-md flex flex-col">
        <div class="flex flex-col flex-1 overflow-y-auto">
            <!-- Encabezado -->
            <div class="p-6 text-xl font-bold text-white flex justify-between items-center">
                <div>
                    <span class="text-center text-xl">Studio Hub</span>
                </div>
            </div>

            <!-- Contenido Scrollable -->
            <nav class="flex flex-col flex-1 mt-4 space-y-1 pb-6 px-2">
                <!-- Dashboard -->
                <Link href="/dashboard"
                    :class="['flex gap-2 items-center px-3 py-3 font-medium rounded-xl text-gray-400 hover:text-white', 
                        isActive('/dashboard') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <!-- Icon placeholder: Dashboard -->
                    <i class="fas fa-tachometer-alt w-6 h-6 text-xl"></i>
                    Panel-Dashboard
                </Link>

                <Link v-if="permissions.finanzas" href="/finances" 
                    :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/finances') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-chart-line w-7 h-7 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Finanzas</span>
                        <span class="text-[0.65rem] ">CRM Clientes-Alianzas-Partners-Empleados</span>
                    </span>
                </Link>

                <Link v-if="permissions.contactos" href="/contacts"
                    :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/contacts') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-database w-7 h-7 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Base de datos</span>
                        <span class="text-[0.65rem] ">CRM Clientes-Alianzas-Partners-Empleados</span>
                    </span>
                </Link>

                <Link v-if="permissions.aprobaciones" href="/approvals"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/approvals') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-check-circle w-7 h-7 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Aprobaciones</span>
                        <span class="text-[0.65rem] ">Tareas- Solicitudes Revision</span>
                    </span>
                </Link>

                 <Link v-if="permissions.donaciones" href="/donations/campaigns"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/donations') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-hand-holding-heart w-7 h-7 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Donaciones</span>
                        <span class="text-[0.65rem] ">Fundaciones Aliadas</span>
                    </span>
                </Link>

                 <Link v-if="permissions['registro-casos']" href="/case-record"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/case-record') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-headset w-6 h-7 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Registro-Casos</span>
                        <span class="text-[0.65rem] ">Registros Casos Comerciales</span>
                    </span>
                </Link>

                 <Link v-if="permissions.reportes" href="/reports"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/reports') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-file-contract w-8 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Reportes</span>
                        <span class="text-[0.65rem] ">Notificaciones</span>
                    </span>
                </Link>

                 <Link v-if="permissions.cotizaciones" href="/quotes"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/quotes') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-calculator w-8 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Cotizaciones</span>
                        <span class="text-[0.65rem] ">Presupuestos</span>
                    </span>
                </Link>

                 <Link v-if="permissions.suscripciones" href="/subs"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/subs') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-shopping-bag w-8 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Suscripciones</span>
                        <span class="text-[0.65rem] ">Todos los Gastos Fijos</span>
                    </span>
                </Link>

                 <Link v-if="permissions.rrhh" href="/rrhh/contracts"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/rrhh') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-users w-8 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">RR.HH/ Contratos</span>
                        <span class="text-[0.65rem] ">Reclutamiento, Base de Datos empleados</span>
                    </span>
                </Link>

                 <Link v-if="permissions.politicas" href="/policies"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/policies') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-file-alt w-8 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Politicas</span>
                        <span class="text-[0.65rem] ">Empresariales- Equipos</span>
                    </span>
                </Link>

                 <Link v-if="permissions.certificados" href="/certificates"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/certificates') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-certificate w-8 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Certificados</span>
                        <span class="text-[0.65rem] ">Empresariales- Bancarios</span>
                    </span>
                </Link>

                 <Link v-if="permissions.licencias" href="/licenses"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/licenses') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                     <i class="fas fa-file-signature w-8 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Tramites y Licencias</span>
                        <span class="text-[0.65rem] ">Gestion documental de licencias para construcción</span>
                    </span>
                </Link>

                 <Link v-if="permissions.proyectos" href="/projects"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/projects') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-project-diagram w-8 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Proyectos</span>
                        <span class="text-[0.65rem] ">Todas las Categorias</span>
                    </span>
                </Link>

                 <Link v-if="permissions.obras" href="/worksites"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/worksites') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-hard-hat w-8 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Obras Construcción</span>
                        <span class="text-[0.65rem] ">Gestion en Obra ELITE 24</span>
                    </span>
                </Link>

                 <Link v-if="permissions.kpis" href="/kpis"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/kpis') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-tasks w-8 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">KPIS/Control calidad</span>
                        <span class="text-[0.65rem] ">Indicaroes KPIS-Indicador clave de rendimiento</span>
                    </span>
                </Link>

                 <Link v-if="permissions.marketing" href="/marketing/strategies"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/marketing') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-bullhorn w-8 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Marketing Elite</span>
                        <span class="text-[0.65rem] ">Estrategias, Gestion, Registros- Novedades</span>
                    </span>
                </Link>

                <Link href="/planner"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/planner') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-clipboard-list w-8 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Planner Task</span>
                    </span>
                </Link>

                <Link href="/calendar"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/calendar') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-calendar-alt w-8 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Agenda Personal</span>
                        <span class="text-[0.65rem] ">Eventos-recordatorios</span>
                    </span>
                </Link>

                 <Link v-if="permissions.cloud" href="/cloud"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/cloud') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-cloud w-8 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Cloud</span>
                        <span class="text-[0.65rem] ">Documents</span>
                    </span>
                </Link>

                 <Link v-if="permissions.reuniones" href="/meetings"
                     :class="['flex gap-2 items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white',
                    isActive('/meetings') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : 'hover:bg-gray-700']">
                    <i class="fas fa-video w-8 text-xl"></i>
                    <span class="flex flex-col">
                        <span class="">Reuniones</span>
                    </span>
                </Link>

            </nav>
        </div>
    </aside>
</template>
