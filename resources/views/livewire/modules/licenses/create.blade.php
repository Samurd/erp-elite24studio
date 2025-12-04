<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ isset($license) ? 'Editar Licencia' : 'Nueva Licencia' }}
                </h1>
                <p class="text-gray-600 mt-1">
                    {{ isset($license) ? 'Actualiza la información de la licencia' : 'Complete los datos para registrar una nueva licencia' }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('licenses.index') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form wire:submit.prevent="save">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Proyecto -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">Proyecto</label>
                    <select id="project_id" wire:model="form.project_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar proyecto</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('form.project_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tipo de Licencia -->
                <div>
                    <label for="license_type_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo de
                        Tramite</label>
                    <select id="license_type_id" wire:model="form.license_type_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar tipo</option>
                        @foreach($licenseTypeOptions as $licenseType)
                            <option value="{{ $licenseType->id }}">{{ $licenseType->name }}</option>
                        @endforeach
                    </select>
                    @error('form.license_type_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="status_id" wire:model="form.status_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar estado</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('form.status_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Entidad -->
                <div>
                    <label for="entity" class="block text-sm font-medium text-gray-700 mb-2">Entidad Tramitadora</label>
                    <input type="text" id="entity" wire:model="form.entity"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Nombre de la entidad tramitadora">
                    @error('form.entity')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Empresa -->
                <div>
                    <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Empresa Gestora</label>
                    <input type="text" id="company" wire:model="form.company"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Nombre de la empresa gestora">
                    @error('form.company')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Número de Erradicado -->
                <div>
                    <label for="eradicated_number" class="block text-sm font-medium text-gray-700 mb-2">Número de
                        Erradicado</label>
                    <input type="text" id="eradicated_number" wire:model="form.eradicated_number"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Número de erradicado">
                    @error('form.eradicated_number')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Segunda Fila - Fechas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                <!-- Fecha Erradicado -->
                <div>
                    <label for="eradicatd_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha
                        Erradicado</label>
                    <input type="date" id="eradicatd_date" wire:model="form.eradicatd_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.eradicatd_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fecha Estimada de Aprobación -->
                <div>
                    <label for="estimated_approval_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha
                        Estimada de Aprobación</label>
                    <input type="date" id="estimated_approval_date" wire:model="form.estimated_approval_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.estimated_approval_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fecha de Vencimiento -->
                <div>
                    <label for="expiration_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha de
                        Vencimiento</label>
                    <input type="date" id="expiration_date" wire:model="form.expiration_date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.expiration_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Tercera Fila -->
            <div class="grid grid-cols-1 gap-6 mt-6">
                <!-- Requiere Prórroga -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">¿Necesita Prórroga?</label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="requires_extension" value="1" wire:model="form.requires_extension"
                                class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Sí</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="requires_extension" value="0" wire:model="form.requires_extension"
                                class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">No</span>
                        </label>
                    </div>
                    @error('form.requires_extension')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Observaciones -->
                <div>
                    <label for="observations" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <textarea id="observations" wire:model="form.observations" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Notas y observaciones adicionales..."></textarea>
                    @error('form.observations')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>


            @livewire('modules.cloud.components.model-attachments-creator', [
                'modelClass' => \App\Models\License::class,
                'areaSlug' => 'licencias'
            ])

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                <a href="{{ route('licenses.index') }}"
                    class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
                <button type="submit"
                    class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>{{ isset($license) ? 'Actualizar' : 'Guardar' }} Licencia
            </button>
        </div>
    </form>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Componente de notificaciones --}}
    @livewire('components.notification')

    {{-- Modal selector de carpetas --}}

</div>