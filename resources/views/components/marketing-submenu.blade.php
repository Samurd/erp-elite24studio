<!-- Submenú Marketing -->
<div class="w-min bg-white border-b shadow-xs  h-12 flex items-center relative rounded-full m-4">
    <div id="scrollContainer"
        class="flex-1 overflow-x-auto whitespace-nowrap flex items-center scrollbar-hide scroll-smooth">
        <ul class="flex items-center space-x-6 text-sm font-medium px-4">
            <li><a href="{{ route('marketing.strategies.index') }}"
                    class="{{ request()->routeIs('marketing.strategies.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600' }} hover:text-yellow-600">Estrategias</a>
            </li>
            <li><a href="{{ route('marketing.socialmedia.index') }}"
                    class="{{ request()->routeIs('marketing.socialmedia.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600' }} hover:text-yellow-600">Redes
                    Sociales & Web</a></li>
            <li><a href="{{ route('marketing.cases.index') }}"
                    class="{{ request()->routeIs('marketing.cases.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600' }} hover:text-yellow-600">Casos</a></li>
            <li><a href="{{ route('marketing.events.index') }}" class="{{ request()->routeIs('marketing.events.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600' }} hover:text-yellow-600">Eventos + Presupuestos + APU de Pauta</a></li>
            <li><a href="{{ route('marketing.ad-pieces.index') }}"
                    class="{{ request()->routeIs('marketing.ad-pieces.*') ? 'text-yellow-600 font-semibold' : 'hover:text-yellow-600' }} hover:text-yellow-600">Piezas Publicitarias</a></li>
        </ul>
    </div>
</div>