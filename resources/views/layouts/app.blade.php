<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <!-- Styles -->
    <!-- @livewireStyles -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.css"> -->
    <!-- FullCalendar CSS -->
    <!-- <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' /> -->
</head>

<body class="font-sans antialiased">
    <x-banner />

    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar -->
        <!-- <div class="w-64">
            @livewire('sidebar')
        </div> -->

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            <!-- Navbar -->
            <!-- <div class="w-full">
                @livewire('navigation-menu')
            </div> -->

            <!-- Donations Submenu -->
            @if(request()->is('donations*'))
                <div class="px-4 py-2">
                    <x-donations-submenu />
                </div>
            @endif

            <!-- RRHH Submenu -->
            @if(request()->is('rrhh*'))
                <div class="px-4 py-2">
                    <x-rrhh-submenu />
                </div>
            @endif

            <!-- Marketing Submenu -->
            @if(request()->is('marketing*'))
                <div class="px-4 py-2">
                    <x-marketing-submenu />
                </div>
            @endif

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="flex-1 p-4 ml-4 overflow-auto">
                {{ $slot }}
            </main>
        </div>
    </div>
    <!-- 
    @stack('modals')
    @stack('styles')
    @livewireScripts -->

    <!-- Modal components -->
    <!-- @livewire('modules.cloud.components.file-selector-modal')
    @livewire('components.folder-selector-modal')
    @livewire('components.notification') -->

    <!-- Cargar Chart.js -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.umd.js"></script> -->
    <!-- FullCalendar JS -->
    <!-- <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/es.global.min.js'></script> -->

    <!-- Emoji Picker -->
    <!-- <script type="module">
        import 'https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js';
    </script> -->

    <!-- Aquí se inyectan los scripts de cada vista -->
    <!-- @stack('scripts')
    @stack('scripts-content') -->
</body>

</html>