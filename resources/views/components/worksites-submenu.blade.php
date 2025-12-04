<!-- Submenú Marketing -->
<div class="w-min bg-white border-b shadow-xs  h-12 flex items-center relative rounded-full m-4">
        <div id="scrollContainer"
                class="flex-1 overflow-x-auto whitespace-nowrap flex items-center scrollbar-hide scroll-smooth">
                <ul class="flex items-center space-x-6 text-sm font-medium px-4">
                        <li><a href="{{ route('marketing.strategies.index') }}"
                                        class="{{ request()->routeIs('marketing.strategies.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600' }} hover:text-yellow-600">Obras
                                        Construcción</a>
                        </li>
                        <li><a href="{{ route('marketing.socialmedia.index') }}"
                                        class="{{ request()->routeIs('marketing.socialmedia.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600' }} hover:text-yellow-600">Visitas
                                        Obras</a></li>
                </ul>
        </div>
</div>