<x-app-layout>
    <div>
        @can($permissionCreate)

            <x-button><a href="{{ route('users.create') }}">Crear nuevo usuario</a></x-button>
            <div>

        @endcan
            @livewire('modules.users.user-table', ['slug' => $slug])
        </div>
    </div>
</x-app-layout>