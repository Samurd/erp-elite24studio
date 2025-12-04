<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detalles del Certificado</h1>
                <p class="text-gray-600 mt-1">Información completa del certificado</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('certificates.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
                @canArea('update', 'certificados')
                <a href="{{ route('certificates.edit', $certificate->id) }}"
                    class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Editar
                </a>
                @endCanArea
            </div>
        </div>
    </div>

    <!-- Certificate Details -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Información Básica</h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">ID</label>
                        <p class="text-sm text-gray-900">#{{ $certificate->id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre</label>
                        <p class="text-sm text-gray-900">{{ $certificate->name }}</p>
                    </div>

                    @if($certificate->description)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Descripción</label>
                            <p class="text-sm text-gray-900">{{ $certificate->description }}</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo</label>
                        @if($certificate->type)
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $certificate->type->name }}
                            </span>
                        @else
                            <p class="text-sm text-gray-500">No especificado</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                        @if($certificate->status)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($certificate->status->name == 'Activo') bg-green-100 text-green-800
                                        @elseif($certificate->status->name == 'Inactivo') bg-red-100 text-red-800
                                        @elseif($certificate->status->name == 'Pendiente') bg-yellow-100 text-yellow-800
                                        @elseif($certificate->status->name == 'En Proceso') bg-blue-100 text-blue-800
                                        @elseif($certificate->status->name == 'Vencido') bg-gray-100 text-gray-800
                                        @elseif($certificate->status->name == 'Prorrogado') bg-teal-100 text-teal-800
                                        @else bg-gray-100 text-gray-800 @endif
                                    ">
                                {{ $certificate->status->name }}
                            </span>
                        @else
                            <p class="text-sm text-gray-500">No especificado</p>
                        @endif
                    </div>
                </div>

                <!-- Dates and Assignment -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Fechas y Asignación</h3>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de Emisión</label>
                        @if($certificate->issued_at)
                            <p class="text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($certificate->issued_at)->format('d/m/Y') }}
                            </p>
                        @else
                            <p class="text-sm text-gray-500">No especificada</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de Vencimiento</label>
                        @if($certificate->expires_at)
                            <p class="text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($certificate->expires_at)->format('d/m/Y') }}
                            </p>
                            @if(\Carbon\Carbon::parse($certificate->expires_at)->isPast())
                                <p class="text-xs text-red-600 mt-1">Vencido</p>
                            @endif
                        @else
                            <p class="text-sm text-gray-500">No especificada</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Asignado a</label>
                        @if($certificate->assignedTo)
                            <p class="text-sm text-gray-900">{{ $certificate->assignedTo->name }}</p>
                            <p class="text-xs text-gray-500">{{ $certificate->assignedTo->email }}</p>
                        @else
                            <p class="text-sm text-gray-500">No asignado</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de Creación</label>
                        <p class="text-sm text-gray-900">{{ $certificate->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    @if($certificate->updated_at && $certificate->updated_at != $certificate->created_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Última Actualización</label>
                            <p class="text-sm text-gray-900">{{ $certificate->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Files Section -->
            <livewire:modules.cloud.components.model-files :model="$certificate" />
        </div>
    </div>
</div>