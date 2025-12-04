<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detalles de Política</h1>
                <p class="text-gray-600 mt-1">Información completa de la política</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('policies.edit', $policy->id) }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                <a href="{{ route('policies.index') }}"
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Policy Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Información General</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ID</label>
                        <p class="text-gray-900">#{{ $policy->id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <p class="text-gray-900">{{ $policy->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                        @if($policy->type)
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $policy->type->name }}
                            </span>
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        @if($policy->status)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($policy->status->name == 'Activa') bg-green-100 text-green-800
                                            @elseif($policy->status->name == 'En Revisión') bg-yellow-100 text-yellow-800
                                            @elseif($policy->status->name == 'Vencida') bg-red-100 text-red-800
                                            @elseif($policy->status->name == 'Suspendida') bg-gray-100 text-gray-800
                                            @else bg-gray-100 text-gray-800 @endif
                                        ">
                                {{ $policy->status->name }}
                            </span>
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                        <p class="text-gray-900">{{ $policy->assignedTo ? $policy->assignedTo->name : '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Emisión</label>
                        <p class="text-gray-900">
                            @if($policy->issued_at)
                                {{ $policy->issued_at->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Última Revisión</label>
                        <p class="text-gray-900">
                            @if($policy->reviewed_at)
                                {{ $policy->reviewed_at->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>

                <!-- URL -->
                @if($policy->url)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL de la Política</label>
                        <a href="{{ $policy->url }}" target="_blank"
                            class="text-blue-600 hover:text-blue-900 text-sm flex items-center"
                            title="Abrir URL de la política">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            {{ $policy->url }}
                        </a>
                    </div>
                @endif
            </div>

            <!-- Description Section -->
            @if($policy->description)
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Descripción</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $policy->description }}</p>
                    </div>
                </div>
            @endif

            <!-- Content Section -->
            @if($policy->content)
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Contenido</h2>
                    <div class="prose max-w-none">
                        <div class="text-gray-700 whitespace-pre-wrap">{{ $policy->content }}</div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <livewire:modules.cloud.components.model-files :model="$policy" />

            <!-- Metadata -->
            <div class="bg-white rounded-lg shadow-sm p-6 mt-3">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Información de Registro</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Creación</label>
                        <p class="text-gray-900">{{ $policy->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Última Actualización</label>
                        <p class="text-gray-900">{{ $policy->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>