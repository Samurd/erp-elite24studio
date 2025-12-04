<div class="p-6 bg-[#252525] text-white min-h-screen space-y-6 rounded-lg">

    {{-- Breadcrumbs --}}
    @if(!$isFileShare)
        <div class="flex items-center space-x-2 mb-2">
            @if ($currentFolder)
                <button wire:click="goBack" class="px-3 py-1 bg-gray-700 rounded hover:bg-gray-600">
                    Volver
                </button>
            @endif

            <div class="flex items-center space-x-2 border border-gray-800 rounded-lg p-1 overflow-x-auto no-scrollbar max-w-full">
                <button wire:click="openFolder({{ $share->shareable->id }})" class="text-gray-300 hover:underline flex-shrink-0">
                    Root
                </button>

                @if (!empty($breadcrumbs))
                    <span class="text-gray-500 flex-shrink-0">/</span>
                @endif

                @foreach ($breadcrumbs as $crumb)
                    <button wire:click="openFolder({{ $crumb->id }})" class="text-blue-400 hover:underline truncate max-w-[200px]" title="{{ $crumb->name }}">
                        {{ $crumb->name }}
                    </button>
                    @if (!$loop->last)
                        <span class="text-gray-500 flex-shrink-0">/</span>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    {{-- Explorador --}}
    <div class="rounded-lg divide-y divide-gray-700">
        {{-- Encabezado --}}
        <div class="flex items-center px-4 py-2 text-gray-300 text-sm font-semibold rounded-t-lg">
            <div class="w-1/4">Nombre</div>
            <div class="w-1/6">Tipo</div>
            <div class="w-1/6">Tamaño</div>
            <div class="w-1/6">Creado</div>
            <div class="w-1/6">Actualizado</div>
            <div class="w-1/6">Usuario</div>
            <div class="w-1/6 text-right">Acciones</div>
        </div>

        {{-- Carpetas --}}
        @if(!$isFileShare)
            @foreach ($folders as $folder)
                <div class="flex items-center px-4 py-2 hover:bg-gray-700 transition rounded-lg cursor-pointer" wire:click="openFolder({{ $folder->id }})">
                    <div class="w-1/4 flex items-center space-x-2">
                        <span class="text-yellow-400 text-lg">📁</span>
                        <span class="font-medium truncate max-w-[200px]" title="{{ $folder->name }}">{{ $folder->name }}</span>
                    </div>
                    <div class="w-1/6 text-sm text-gray-400">Carpeta</div>
                    <div class="w-1/6 text-sm text-gray-400">—</div>
                    <div class="w-1/6 text-sm text-gray-400">{{ $folder->created_at->format('d/m/Y h:i A') }}</div>
                    <div class="w-1/6 text-sm text-gray-400">{{ $folder->updated_at->format('d/m/Y h:i A') }}</div>
                    <div class="w-1/6 flex items-center space-x-2">
                        <img src="{{ $folder->user->profile_photo_url ?? '' }}" class="w-6 h-6 rounded-full object-cover" alt="">
                        <span class="text-gray-400 text-sm">{{ $folder->user->name ?? '—' }}</span>
                    </div>
                    <div class="w-1/6 text-right text-sm text-gray-400">—</div>
                </div>
            @endforeach
        @endif

        {{-- Archivos --}}
        @foreach ($files as $file)
            <div class="flex items-center px-4 py-2 hover:bg-gray-700 transition rounded-lg">
                <div class="w-1/4 flex items-center space-x-2">
                    <span class="text-blue-400 text-lg">📄</span>
                    <span class="font-medium truncate max-w-[200px]" title="{{ $file->name }}">{{ $file->name }}</span>
                </div>
                <div class="w-1/6 text-sm text-gray-400">{{ $file->mime_type }}</div>
                <div class="w-1/6 text-sm text-gray-400">{{ $file->readable_size }}</div>
                <div class="w-1/6 text-sm text-gray-400">{{ $file->created_at->format('d/m/Y h:i A') }}</div>
                <div class="w-1/6 text-sm text-gray-400">{{ $file->updated_at->format('d/m/Y h:i A') }}</div>
                <div class="w-1/6 flex items-center space-x-2">
                    <img src="{{ $file->user->profile_photo_url ?? '' }}" class="w-6 h-6 rounded-full object-cover" alt="">
                    <span class="text-gray-400 text-sm">{{ $file->user->name ?? '—' }}</span>
                </div>
                <div class="w-1/6 text-right text-sm">
                    <a href="{{ $file->url }}" target="_blank" class="text-blue-400 hover:underline">Ver</a>
                </div>
            </div>
        @endforeach
    </div>
</div>
