<!-- Submenú Donations -->
<div class="w-min bg-white border-b shadow-xs  h-12 flex items-center relative rounded-full m-4">
        <div id="scrollContainer"
                class="flex-1 overflow-x-auto whitespace-nowrap flex items-center scrollbar-hide scroll-smooth">
                <ul class="flex items-center space-x-6 text-sm font-medium px-4">
                        <li><a href="{{ route('donations.campaigns.index') }}"
                                        class="{{ request()->routeIs('donations.campaigns.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600' }} hover:text-yellow-600">Campañas</a>
                        </li>
                        <li><a href="{{ route('donations.donations.index') }}"
                                        class="{{ request()->routeIs('donations.donations.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600' }} hover:text-yellow-600">Donaciones</a>
                        </li>
                        <li><a href="#" class="hover:text-yellow-600">Calendario</a>
                        </li>
                        <li><a href="{{ route('donations.volunteers.index') }}"
                                        class="{{ request()->routeIs('donations.volunteers.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600' }}hover:text-yellow-600">Voluntariado</a>
                        </li>
                        <!-- <li><a href="#" class="hover:text-yellow-600">Certificados</a> -->
                        </li>
                        <li><a href="{{ route('donations.alliances.index') }}"
                                        class="{{ request()->routeIs('donations.alliances.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600' }} hover:text-yellow-600">Alianzas/Convenios</a>
                        </li>
                        <li><a href="{{ route('donations.apu-campaigns.index') }}"
                                        class="{{ request()->routeIs('donations.apu-campaigns.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600' }} hover:text-yellow-600">Control
                                        Presupuestos APU</a>
                        </li>
                </ul>
        </div>
</div>