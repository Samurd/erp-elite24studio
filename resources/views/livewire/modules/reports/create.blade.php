<div>
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                {{ isset($report) ? 'Editar Reporte' : 'Nuevo Reporte' }}
            </h1>
        </div>

        <form wire:submit="save" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Título -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                    <input type="text" id="title" wire:model="form.title"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.title')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea id="description" wire:model="form.description" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"></textarea>
                    @error('form.description')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Fecha -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
                    <input type="date" id="date" wire:model="form.date"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Hora -->
                <div>
                    <label for="hour" class="block text-sm font-medium text-gray-700 mb-2">Hora</label>
                    <input type="time" id="hour" wire:model="form.hour"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.hour')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="status_id" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select id="status_id" wire:model="form.status_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccione un estado</option>
                        @foreach($statusOptions as $status)
                            <option value="{{ $status->id }}">{{ $status->name }}</option>
                        @endforeach
                    </select>
                    @error('form.status_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Notas -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notas</label>
                    <textarea id="notes" wire:model="form.notes" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"></textarea>
                    @error('form.notes')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>


            @if(isset($report))
                @livewire('modules.cloud.components.model-attachments', [
                    'model' => $report,
                    'area' => 'reportes'
                ])
            @else
                @livewire('modules.cloud.components.model-attachments-creator', [
                    'modelClass' => \App\Models\Report::class,
                    'areaSlug' => 'reportes'
                ])
            @endif

            <div class="flex justify-end space-x-3 pt-6 border-t">
                <a href="{{ route('reports.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    {{ isset($report) ? 'Actualizar Reporte' : 'Guardar Reporte' }}
                </button>
            </div>
        </form>
    </div>
</div>