<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ isset($adpiece) ? 'Editar Pieza Publicitaria' : 'Nueva Pieza Publicitaria' }}
                </h1>
                <p class="text-gray-600 mt-1">
                    {{ isset($adpiece) ? 'Actualiza la información de la pieza publicitaria' : 'Complete los datos para registrar una nueva pieza publicitaria' }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('marketing.ad-pieces.index') }}"
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
                <!-- Nombre -->
                <div class="lg:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Pieza</label>
                    <input type="text" id="name" wire:model="form.name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Nombre descriptivo de la pieza publicitaria">
                    @error('form.name')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tipo -->
                <div>
                    <label for="type_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                    <select id="type_id" wire:model="form.type_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar tipo</option>
                        @foreach($typeOptions as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('form.type_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Formato -->
                <div>
                    <label for="format_id" class="block text-sm font-medium text-gray-700 mb-2">Formato</label>
                    <select id="format_id" wire:model="form.format_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar formato</option>
                        @foreach($formatOptions as $format)
                            <option value="{{ $format->id }}">{{ $format->name }}</option>
                        @endforeach
                    </select>
                    @error('form.format_id')
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

                <!-- Medio -->
                <div>
                    <label for="media" class="block text-sm font-medium text-gray-700 mb-2">Medio</label>
                    <input type="text" id="media" wire:model="form.media"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Instagram, TikTok, Facebook, etc.">
                    @error('form.media')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Segunda Fila - Relaciones -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
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

                <!-- Equipo Responsable -->
                <div>
                    <label for="team_id" class="block text-sm font-medium text-gray-700 mb-2">Equipo Responsable</label>
                    <select id="team_id" wire:model="form.team_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar equipo</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                    @error('form.team_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Estrategia -->
                <div>
                    <label for="strategy_id" class="block text-sm font-medium text-gray-700 mb-2">Estrategia</label>
                    <select id="strategy_id" wire:model="form.strategy_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar estrategia</option>
                        @foreach($strategies as $strategy)
                            <option value="{{ $strategy->id }}">{{ $strategy->name }}</option>
                        @endforeach
                    </select>
                    @error('form.strategy_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Tercera Fila - Instrucciones -->
            <div class="grid grid-cols-1 gap-6 mt-6">
                <!-- Instrucciones -->
                <div>
                    <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">Instrucciones</label>
                    <textarea id="instructions" wire:model="form.instructions" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                        placeholder="Instrucciones, comentarios o detalles adicionales sobre la pieza..."></textarea>
                    @error('form.instructions')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>


            @if(isset($adpiece))
                @livewire('modules.cloud.components.model-attachments', [
                    'model' => $adpiece,
                    'area' => 'marketing'
                ])
            @else
                @livewire('modules.cloud.components.model-attachments-creator', [
                    'modelClass' => \App\Models\Adpiece::class,
                    'areaSlug' => 'marketing'
                ])
             @endif

            <!-- Botones de Acción -->
        <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                <a href="{{ route('marketing.ad-pieces.index') }}"
                   class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
                <button type="submit"
                        class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>{{ isset($adpiece) ? 'Actualizar' : 'Guardar' }} Pieza
            </button>
        </div>
    </form>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
</div>
