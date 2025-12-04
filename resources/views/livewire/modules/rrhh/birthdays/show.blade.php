<div>
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Detalle de Cumpleaños
            </h1>
            <div class="flex space-x-3">
                <a href="{{ route('rrhh.birthdays.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                <a href="{{ route('rrhh.birthdays.edit', $birthday->id) }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <button wire:click="delete" wire:confirm="¿Estás seguro de que quieres eliminar este cumpleaños?"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-2"></i>Eliminar
                </button>
            </div>
        </div>

        <!-- Birthday Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Nombre</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $birthday->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Cumpleaños</label>
                    <p class="text-lg text-gray-900">
                        {{ $birthday->date ? \Carbon\Carbon::parse($birthday->date)->format('d/m/Y') : '-' }}
                    </p>
                    <p class="text-sm text-gray-600">
                        {{ $birthday->date ? \Carbon\Carbon::parse($birthday->date)->format('l, F j') : '' }}
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Tipo de Relación</label>
                    @if($birthday->type)
                        <span
                            class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                            {{ $birthday->type->name }}
                        </span>
                    @else
                        <p class="text-lg text-gray-500">-</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Proyecto</label>
                    <p class="text-lg text-gray-900">{{ $birthday->project->name ?? '-' }}</p>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">WhatsApp</label>
                    @if($birthday->whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $birthday->whatsapp) }}" target="_blank"
                            class="text-lg text-green-600 hover:text-green-800">
                            <i class="fab fa-whatsapp mr-1"></i>{{ $birthday->whatsapp }}
                        </a>
                    @else
                        <p class="text-lg text-gray-500">-</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Responsable de Registro</label>
                    <p class="text-lg text-gray-900">{{ $birthday->responsible->name ?? '-' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Fecha de Registro</label>
                    <p class="text-sm text-gray-600">
                        {{ $birthday->created_at ? $birthday->created_at->format('d/m/Y H:i') : '-' }}
                    </p>
                </div>
            </div>
        </div>

        @if($birthday->comments)
            <div class="mt-6 pt-6 border-t">
                <label class="block text-sm font-medium text-gray-500 mb-2">Comentarios</label>
                <p class="text-gray-900 whitespace-pre-wrap">{{ $birthday->comments }}</p>
            </div>
        @endif
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
</div>