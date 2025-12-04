<!-- Submenú RRHH -->
<div class="w-[90%] bg-white border-b shadow-xs  h-12 flex items-center relative rounded-full m-4">
        <div id="scrollContainer"
                class="flex-1 overflow-x-auto whitespace-nowrap flex items-center scrollbar-hide scroll-smooth">
                <ul class="flex items-center space-x-6 text-sm font-medium px-4 min-w-fit mx-auto">
                        <li><a href="{{ route('rrhh.contracts.index') }}"
                                        class="{{ request()->routeIs('rrhh.contracts.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600' }} ml-7">Contratos</a>
                        </li>
                        <li><a href="{{ route('rrhh.employees.index') }}"
                                        class="{{ request()->routeIs('rrhh.employees.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600' }}">Empleados</a>
                        </li>
                        <li><a href="{{ route('rrhh.vacancies.index') }}"
                                        class="{{ request()->routeIs('rrhh.vacancies.*') || request()->routeIs('rrhh.applicants.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600' }}">Reclutamiento</a>
                        </li>
                        <li><a href="{{ route('rrhh.interviews.index') }}"
                                        class="{{ request()->routeIs('rrhh.interviews.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600' }}">Entrevistas</a>
                        </li>
                        <li><a href="{{ route('rrhh.interviews.calendar') }}"
                                        class="{{ request()->routeIs('rrhh.interviews.calendar') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600' }}">Calendario
                                        Entrevistas</a></li>
                        <li><a href="{{ route('rrhh.inductions.index') }}"
                                        class="{{ request()->routeIs('rrhh.inductions.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600' }}hover:text-purple-600">Inducciones</a>
                        </li>
                        <li><a href="{{ route('rrhh.kits.index') }}"
                                        class="{{ request()->routeIs('rrhh.kits.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600' }}hover:text-purple-600">Kits
                                        Bienvenida</a></li>
                        <li><a href="#" class="hover:text-purple-600">Evaluaciones</a></li>
                        <li><a href="{{ route('rrhh.birthdays.index') }}"
                                        class="{{ request()->routeIs('rrhh.birthdays.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600' }}hover:text-purple-600">Cumpleaños</a>
                        </li>
                        <li><a href="{{ route('rrhh.offboardings.index') }}"
                                        class="{{ request()->routeIs('rrhh.offboardings.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600' }}hover:text-purple-600">Off-boarding</a>
                        </li>
                        <li><a href="{{ route('rrhh.holidays.index') }}"
                                        class="{{ request()->routeIs('rrhh.holidays.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600' }}hover:text-purple-600">Vacaciones/Permisos
                                        med.</a></li>
                        <li><a href="{{ route('rrhh.attendances.index') }}"
                                        class="{{ request()->routeIs('rrhh.attendances.*') ? 'text-purple-600 font-semibold' : 'hover:text-purple-600' }}hover:text-purple-600">Asistencia</a>
                        </li>
                </ul>
        </div>
</div>