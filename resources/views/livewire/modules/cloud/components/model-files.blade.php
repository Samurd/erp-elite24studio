<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-4 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-semibold text-gray-800">Archivos Adjuntos</h3>
        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">{{ $files->count() }} archivos</span>
    </div>

    <div class="divide-y divide-gray-100">
        @forelse($files as $file)
            <div class="p-3 flex items-center justify-between hover:bg-gray-50 transition-colors group">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <div class="flex-shrink-0 text-gray-400 group-hover:text-blue-500 transition-colors">
                        {{-- Icon based on file type could go here, using a generic file icon for now --}}
                        <x-clarity-file-solid class="w-8 h-8" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('files.view', $file) }}" target="_blank"
                            class="text-sm font-medium text-gray-700 hover:text-blue-600 truncate block transition-colors"
                            title="{{ $file->name }}">
                            {{ $file->name }}
                        </a>
                        <div class="flex items-center text-xs text-gray-400 space-x-2">
                            <span>{{ $file->readable_size }}</span>
                            <span>&bull;</span>
                            <span>{{ $file->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    {{-- Download --}}
                    <a href="{{ route('files.download', $file) }}"
                        class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors"
                        title="Descargar">
                        <x-clarity-download-cloud-line class="w-5 h-5" />
                    </a>

                    {{-- Detach --}}
                    @can('update', $file)
                        <button wire:click="detach({{ $file->id }})"
                            wire:confirm="¿Estás seguro de desvincular este archivo? No se eliminará del sistema."
                            class="p-1.5 text-gray-400 hover:text-orange-500 hover:bg-orange-50 rounded transition-colors"
                            title="Desvincular">
                            <x-lucide-unlink class="w-5 h-5" />
                        </button>
                    @endcan

                    {{-- Delete --}}
                    @can('delete', $file)
                        <button wire:click="delete({{ $file->id }})"
                            wire:confirm="¿Estás seguro? Esto eliminará el archivo permanentemente del sistema."
                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors"
                            title="Eliminar permanentemente">
                            <x-fluentui-delete-48-o class="w-5 h-5" />
                        </button>
                    @endcan
                </div>
            </div>
        @empty
            <div class="p-8 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 text-gray-400 mb-3">
                    <x-clarity-file-line class="w-6 h-6" />
                </div>
                <p class="text-sm text-gray-500">No hay archivos adjuntos visibles.</p>
            </div>
        @endforelse
    </div>
</div>