<div>
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ isset($post) ? 'Editar Publicación' : 'Nueva Publicación' }}
                </h1>
                <p class="text-gray-600 mt-1">
                    {{ isset($post) ? 'Actualiza la información de la publicación' : 'Complete los datos para registrar una nueva publicación en redes sociales' }}
                </p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('marketing.socialmedia.index') }}"
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
                <!-- Nombre de Pieza -->
                <div class="lg:col-span-2">
                    <label for="piece_name" class="block text-sm font-medium text-gray-700 mb-2">Nombre de la Pieza *</label>
                    <input type="text" id="piece_name" wire:model="form.piece_name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                           placeholder="Ej: Campaña de Verano 2024">
                    @error('form.piece_name')
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
            </div>

            <!-- Medios y Tipo de Contenido -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Medios -->
                <div>
                    <label for="mediums" class="block text-sm font-medium text-gray-700 mb-2">Medios</label>
                    <input type="text" id="mediums" wire:model="form.mediums"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                           placeholder="Ej: Instagram, TikTok, Facebook, LinkedIn">
                    @error('form.mediums')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Tipo de Contenido -->
                <div>
                    <label for="content_type" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Contenido</label>
                    <input type="text" id="content_type" wire:model="form.content_type"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                           placeholder="Ej: Carrusel, Reel, Blog, Video, Imagen">
                    @error('form.content_type')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Proyecto, Fecha Programación y Responsable -->
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

                <!-- Fecha de Programación -->
                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Programación</label>
                    <input type="date" id="scheduled_date" wire:model="form.scheduled_date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    @error('form.scheduled_date')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Responsable -->
                <div>
                    <label for="responsible_id" class="block text-sm font-medium text-gray-700 mb-2">Responsable</label>
                    <select id="responsible_id" wire:model="form.responsible_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500">
                        <option value="">Seleccionar responsable</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('form.responsible_id')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Comentarios -->
            <div class="grid grid-cols-1 gap-6 mt-6">
                <div>
                    <label for="comments" class="block text-sm font-medium text-gray-700 mb-2">Comentarios</label>
                    <textarea id="comments" wire:model="form.comments" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500"
                              placeholder="Notas adicionales, instrucciones, etc..."></textarea>
                    @error('form.comments')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            @if(isset($post))
                @livewire('modules.cloud.components.model-attachments', [
                    'model' => $post,
                    'area' => 'marketing'
                ])
            @else
                @livewire('modules.cloud.components.model-attachments-creator', [
                    'modelClass' => \App\Models\SocialMediaPost::class,
                    'areaSlug' => 'marketing'
                ])
            @endif

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t">
                <a href="{{ route('marketing.socialmedia.index') }}"
                   class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </a>
                <button type="submit"
                        class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>{{ isset($post) ? 'Actualizar' : 'Guardar' }} Publicación
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
