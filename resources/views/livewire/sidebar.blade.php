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
            <a href="{{ route('dashboard') }}"
                class="flex gap-2 items-center px-3 py-3 font-medium rounded-xl text-gray-400 hover:text-white
               {{ request()->routeIs('dashboard') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}">
                <x-ri-dashboard-fill class="w-6 h-6" />

                Panel-Dashboard
            </a>

            @canArea('view', 'finanzas')
            <a href="{{ route('finances.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('finances.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-ionicon-stats-chart class="w-7 h-7" />
                <span class="flex flex-col ">
                    <span class="">Finanzas</span>
                    <span class="text-[0.65rem] ">CRM Clientes-Alianzas-Partners-Empleados</span>
                </span>
            </a>
            @endCanArea



            <!-- Datos -->

            @canArea('view', 'contactos')
            <a href="{{ route('contacts.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('contacts.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white ' : ' hover:bg-gray-700' }}
            ">
                <x-bi-database-fill class="w-7 h-7" />
                <span class="flex flex-col ">
                    <span class="">Base de datos</span>
                    <span class="text-[0.65rem] ">CRM Clientes-Alianzas-Partners-Empleados</span>
                </span>
            </a>
            @endCanArea


            @canArea('view', 'aprobaciones')
            <a href="{{ route('approvals.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('approvals.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-fluentui-document-checkmark-20 class="w-7 h-7" />

                <span class="flex flex-col ">
                    <span class="">Aprobaciones</span>
                    <span class="text-[0.65rem] ">Tareas- Solicitudes Revision</span>
                </span>
            </a>
            @endCanArea

            @canArea('view', 'donaciones')
            <a href="{{ route('donations.campaigns.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('donations.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-fluentui-handshake-16 class="w-7 h-7" />

                <span class="flex flex-col ">
                    <span class="">Donaciones</span>
                    <span class="text-[0.65rem] ">Fundaciones Aliadas</span>
                </span>
            </a>
            @endCanArea

            @canArea('view', 'registro-casos')
            <a href="{{ route('case-record.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('case-record.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-fas-headset class="w-6 h-7" />
                <span class="flex flex-col ">
                    <span class="">Registro-Casos</span>
                    <span class="text-[0.65rem] ">Registros Casos Comerciales</span>
                </span>
            </a>
            @endCanArea


            @canArea('view', 'reportes')
            <a href="{{ route('reports.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-tabler-report-search class="w-8" />

                <span class="flex flex-col ">
                    <span class="">Reportes</span>
                    <span class="text-[0.65rem] ">Notificaciones</span>
                </span>
            </a>
            @endCanArea

            @canArea('view', 'cotizaciones')
            <a href="{{ route('quotes.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('quotes.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-clarity-calculator-solid class="w-8" />

                <span class="flex flex-col ">
                    <span class="">Cotizaciones</span>
                    <span class="text-[0.65rem] ">Presupuestos</span>
                </span>
            </a>
            @endCanArea


            @canArea('view', 'suscripciones')
            <a href="{{ route('subs.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('subs.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-clarity-shopping-bag-solid class="w-8" />

                <span class="flex flex-col ">
                    <span class="">Suscripciones</span>
                    <span class="text-[0.65rem] ">Todos los Gastos Fijos</span>
                </span>
            </a>
            @endCanArea

            @canArea('view', 'suscripciones')
            <a href="{{ route('rrhh.contracts.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('rrhh.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-fas-people-group class="w-8" />

                <span class="flex flex-col ">
                    <span class="">RR.HH/ Contratos</span>
                    <span class="text-[0.65rem] ">Reclutamiento, Base de Datos empleados</span>
                </span>
            </a>
            @endCanArea




            @canArea('view', 'politicas')
            <a href="{{ route('policies.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('policies.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-fluentui-document-text-toolbox-20 class="w-8" />

                <span class="flex flex-col ">
                    <span class="">Politicas</span>
                    <span class="text-[0.65rem] ">Empresariales- Equipos</span>
                </span>
            </a>
            @endCanArea



            @canArea('view', 'certificados')
            <a href="{{ route('certificates.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('certificates.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-fluentui-document-ribbon-48 class="w-8" />

                <span class="flex flex-col ">
                    <span class="">Certificados</span>
                    <span class="text-[0.65rem] ">Empresariales- Bancarios</span>
                </span>
            </a>
            @endCanArea

            @canArea('view', 'licencias')
            <a href="{{ route('licenses.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('licenses.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-clarity-license-solid class="w-8" />

                <span class="flex flex-col ">
                    <span class="">Tramites y Licencias</span>
                    <span class="text-[0.65rem] ">Gestion documental de licencias para construcción</span>
                </span>
            </a>
            @endCanArea
            @canArea('view', 'proyectos')
            <a href="{{ route('projects.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('projects.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-clarity-event-outline-badged class="w-8" />

                <span class="flex flex-col ">
                    <span class="">Proyectos</span>
                    <span class="text-[0.65rem] ">Todas las Categorias</span>
                </span>
            </a>
            @endCanArea
            @canArea('view', 'obras')
            <a href="{{ route('worksites.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('worksites.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-lucide-construction class="w-8" />

                <span class="flex flex-col ">
                    <span class="">Obras Construcción</span>
                    <span class="text-[0.65rem] ">Gestion en Obra ELITE 24</span>
                </span>
            </a>
            @endCanArea



            @canArea('view', 'kpis')
            <a href="{{ route('kpis.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('kpis.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-clarity-checkbox-list-line class="w-8" />

                <span class="flex flex-col ">
                    <span class="">KPIS/Control calidad</span>
                    <span class="text-[0.65rem] ">Indicaroes KPIS-Indicador clave de rendimiento</span>
                </span>
            </a>
            @endCanArea


            @canArea('view', 'marketing')
            <a href="{{ route('marketing.strategies.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('marketing.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-clarity-world-line class="w-8" />

                <span class="flex flex-col ">
                    <span class="">Marketing Elite</span>
                    <span class="text-[0.65rem] ">Estrategias, Gestion, Registros- Novedades</span>
                </span>
            </a>
            @endCanArea

            <a href="{{ route('planner.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('planner.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-clarity-timeline-line class="w-8" />



                <span class="flex flex-col ">
                    <span class="">Planner Task</span>
                    {{-- <span class="text-[0.65rem] ">Eventos-recordatorios</span> --}}
                </span>
            </a>
            <a href="{{ route('calendar.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('calendar.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-clarity-date-solid class="w-8" />


                <span class="flex flex-col ">
                    <span class="">Agenda Personal</span>
                    <span class="text-[0.65rem] ">Eventos-recordatorios</span>
                </span>
            </a>


            @canArea('view', 'cloud')
            <a href="{{ route('cloud.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('cloud.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-clarity-upload-cloud-line class="w-8" />

                <span class="flex flex-col ">
                    <span class="">Cloud</span>
                    <span class="text-[0.65rem] ">Documents</span>
                </span>
            </a>
            @endCanArea
            @canArea('view', 'reuniones')
            <a href="{{ route('meetings.index') }}" class="flex gap-2  items-center py-2 px-1 rounded-xl text-gray-400 hover:text-white
            {{ request()->routeIs('meetings.*') ? 'bg-gradient-to-r from-black to-yellow-600 text-white' : ' hover:bg-gray-700' }}
            ">
                <x-clarity-video-camera-solid class="w-8" />

                <span class="flex flex-col ">
                    <span class="">Reuniones</span>
                </span>
            </a>
            @endCanArea


        </nav>
    </div>
</aside>