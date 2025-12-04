<div x-data="{
    tab: 'group',
    activePlan: @entangle('selectedPlan'),
    changeTab(newTab) {
        this.tab = newTab;
        this.activePlan = null;
        $wire.resetSelection();
    }
}" class="p-6">

    <!-- === TABS PRINCIPALES === -->
    <div class="flex justify-between gap-4 border-b border-gray-200 mb-4">
        <div>
            <button @click="changeTab('group')"
                :class="tab === 'group' ? 'border-b-2 border-yellow-500 text-yellow-600' : 'text-gray-500'"
                class="px-4 py-2 font-semibold">
                Otros planes/proyectos
            </button>

            <button @click="changeTab('personal')"
                :class="tab === 'personal' ? 'border-b-2 border-yellow-500 text-yellow-600' : 'text-gray-500'"
                class="px-4 py-2 font-semibold">
                Planes personales
            </button>
        </div>

        <div>
            <x-button x-on:click="$wire.dispatch('open-create-plan-modal')">Crear Plan</x-button>
        </div>
    </div>

    <!-- === LISTA DE PLANES (Unified) === -->
    <div class="mb-4">
        <!-- Group Plans -->
        <div x-show="tab === 'group'" class="flex overflow-x-auto space-x-3 pb-2">
            @if ($groupPlans->isNotEmpty())
                @foreach ($groupPlans as $plan)
                    <button wire:key="group-plan-{{ $plan->id }}"
                        @click="activePlan = {{ $plan->id }}; $wire.set('selectedPlan', {{ $plan->id }});"
                        class="whitespace-nowrap px-4 py-2 text-sm rounded-md" :class="activePlan == {{ $plan->id }} ?
                                    'bg-yellow-500 text-white font-semibold' :
                                    'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                        {{ $plan->name }}
                    </button>
                @endforeach
            @else
                <p class="text-gray-500 text-sm italic px-4 py-2">No hay planes grupales.</p>
            @endif
        </div>

        <!-- Personal Plans -->
        <div x-show="tab === 'personal'" class="flex overflow-x-auto space-x-3 pb-2" x-cloak>
            @if ($personalPlans->isNotEmpty())
                @foreach ($personalPlans as $plan)
                    <button wire:key="personal-plan-{{ $plan->id }}"
                        @click="activePlan = {{ $plan->id }}; $wire.set('selectedPlan', {{ $plan->id }});"
                        class="whitespace-nowrap px-4 py-2 text-sm rounded-md" :class="activePlan == {{ $plan->id }} ?
                                    'bg-yellow-500 text-white font-semibold' :
                                    'bg-gray-100 text-gray-700 hover:bg-gray-200'">
                        {{ $plan->name }}
                    </button>
                @endforeach
            @else
                <p class="text-gray-500 text-sm italic px-4 py-2">No hay planes personales.</p>
            @endif
        </div>
    </div>

    <!-- === CONTENIDO DEL PLAN (Unified) === -->
    <template x-if="activePlan">
        <div>
            <!-- Toolbar -->
            <div class="flex justify-between mb-4">
                <div class="flex gap-2">
                    <x-button x-on:click="$wire.dispatch('open-edit-plan-modal', { planId: activePlan })">
                        Editar plan
                    </x-button>
                    <x-button
                        x-on:click="if(confirm('¿Estás seguro de que deseas eliminar este plan? Esta acción eliminará todos los buckets y tareas asociados.')) { $wire.deletePlan(activePlan); }"
                        class="bg-red-600 hover:bg-red-700">
                        Eliminar plan
                    </x-button>
                </div>
            </div>

            <!-- Buckets Container -->
            <div class="relative w-full overflow-auto max-w-7xl">
                <!-- Loader -->
                <div wire:loading.flex wire:target="selectedPlan"
                    class="bg-white/70 backdrop-blur-sm flex items-center justify-center z-10 rounded-lg p-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-4 border-yellow-500 border-t-transparent">
                    </div>
                </div>

                <div class="flex gap-4 min-w-max items-start pb-2" wire:loading.remove wire:target="selectedPlan">

                    <!-- Buckets existentes -->
                    <div class="flex gap-4 min-w-max items-start" x-ref="bucketsContainer"
                        x-init="window.initBucketSorting($refs.bucketsContainer, $wire)">
                        @foreach ($buckets as $bucket)
                            <div class="bg-gray-50 rounded-lg shadow-sm p-3 border border-gray-200 min-w-[20rem]"
                                data-id="{{ $bucket->id }}" wire:key="bucket-{{ $bucket->id }}">
                                <div class="flex justify-between items-center mb-2">
                                    <!-- Inline Edit Bucket Name -->
                                    <div x-data="{
                                            isEditing: false,
                                            name: '{{ $bucket->name }}',
                                            originalName: '{{ $bucket->name }}',
                                            save() {
                                                this.isEditing = false;
                                                if (this.name.trim() !== '' && this.name !== this.originalName) {
                                                    $wire.updateBucketName({{ $bucket->id }}, this.name);
                                                    this.originalName = this.name;
                                                } else {
                                                    this.name = this.originalName;
                                                }
                                            }
                                        }" class="flex-1 mr-2">
                                        <h3 x-show="!isEditing"
                                            @dblclick="isEditing = true; $nextTick(() => $refs.bucketNameInput.focus())"
                                            class="font-semibold text-sm cursor-pointer hover:text-yellow-600 transition-colors"
                                            title="Doble click para editar">
                                            <span x-text="name"></span>
                                        </h3>
                                        <input x-show="isEditing" x-ref="bucketNameInput" x-model="name"
                                            @keydown.enter="save()" @click.outside="save()"
                                            @keydown.escape="isEditing = false; name = '{{ $bucket->name }}'" type="text"
                                            class="w-full text-sm font-semibold border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring-yellow-500 py-0 px-1 h-6">
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-400">
                                            {{ $bucket->tasks->count() }}
                                        </span>

                                        <div class="text-right relative" x-data="{ open: false }">
                                            <!-- Botón ⋮ -->
                                            <button @click="open = !open"
                                                class="text-gray-500 hover:text-gray-800 focus:outline-none w-4">
                                                ⋮
                                            </button>

                                            <!-- Menú contextual -->
                                            <div x-show="open" @click.outside="open = false" x-transition
                                                class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                                                <ul class="py-1 text-sm text-gray-700">
                                                    <li>
                                                        <button wire:click="deleteBucket({{ $bucket->id }})"
                                                            @click="open = false"
                                                            class="flex w-full items-center px-4 py-2 hover:bg-gray-100">
                                                            Eliminar
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button class="w-full text-center hover:bg-gray-100 py-2 mb-2 rounded-md text-gray-500"
                                    @click="$dispatch('open-create-task-modal', { bucketId: {{ $bucket->id }} })">
                                    + Crear tarea
                                </button>

                                <div class="min-h-[2rem]" data-bucket-id="{{ $bucket->id }}"
                                    x-init="window.initTaskSorting($el, $wire)">
                                    @foreach ($bucket->tasks as $task)
                                        <div class="bg-white border border-gray-200 rounded-md p-3 mb-2 shadow-sm hover:shadow-md transition-shadow cursor-pointer"
                                            data-id="{{ $task->id }}" wire:key="task-{{ $task->id }}"
                                            wire:click="$dispatch('open-edit-task-modal', { bucketId: {{ $bucket->id }}, taskId: {{ $task->id }} })">

                                            <!-- Header with title and menu -->
                                            <div class="flex justify-between items-start mb-2">
                                                <p class="font-medium text-sm text-gray-900 flex-1 pr-2">
                                                    {{ $task->title }}
                                                </p>

                                                <div class="text-right relative z-20" x-data="{ open: false }" @click.stop>
                                                    <button @click="open = !open"
                                                        class="text-gray-500 hover:text-gray-700 focus:outline-none px-2 hover:bg-gray-100 rounded-full transition-colors">
                                                        ⋮
                                                    </button>

                                                    <div x-show="open" @click.outside="open = false" x-transition
                                                        class="absolute right-0 mt-2 w-32 bg-white border border-gray-200 rounded-lg shadow-lg z-10">
                                                        <ul class="py-1 text-sm text-gray-700">
                                                            <li>
                                                                <button wire:click="deleteTask({{ $task->id }})"
                                                                    @click="open = false"
                                                                    class="flex w-full items-center px-3 py-2 hover:bg-red-50 hover:text-red-600">
                                                                    <i class="fas fa-trash text-xs mr-2"></i>Eliminar
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Status and Priority Badges -->
                                            <div class="flex flex-wrap gap-2 mb-2">
                                                @if ($task->status)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                                    @if (Str::lower($task->status->name) == 'completado' || Str::lower($task->status->name) == 'completada') bg-green-100 text-green-800
                                                                    @elseif(Str::lower($task->status->name) == 'en progreso') bg-blue-100 text-blue-800
                                                                    @elseif(Str::lower($task->status->name) == 'pendiente') bg-yellow-100 text-yellow-800
                                                                    @else bg-gray-100 text-gray-800 @endif">
                                                        <i class="fas fa-circle text-[6px] mr-1.5"></i>
                                                        {{ $task->status->name }}
                                                    </span>
                                                @endif

                                                @if ($task->priority)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                                    @if (Str::lower($task->priority->name) == 'alta' || Str::lower($task->priority->name) == 'urgente') bg-red-100 text-red-800
                                                                    @elseif(Str::lower($task->priority->name) == 'media') bg-orange-100 text-orange-800
                                                                    @elseif(Str::lower($task->priority->name) == 'baja') bg-gray-100 text-gray-600
                                                                    @else bg-purple-100 text-purple-800 @endif">
                                                        <i class="fas fa-flag text-[8px] mr-1.5"></i>
                                                        {{ $task->priority->name }}
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Footer: Date and Assigned Users -->
                                            <div
                                                class="flex justify-between items-center text-xs pt-2 border-t border-gray-100">
                                                @if ($task->due_date)
                                                    <div class="flex items-center gap-1 text-gray-500">
                                                        <i class="far fa-calendar text-xs"></i>
                                                        <span>{{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}</span>
                                                    </div>
                                                @else
                                                    <div></div>
                                                @endif

                                                <div class="flex items-center gap-1">
                                                    @if ($task->assignedUsers && $task->assignedUsers->count() > 0)
                                                        <div class="flex items-center -space-x-2">
                                                            @foreach ($task->assignedUsers->take(3) as $user)
                                                                <div class="w-6 h-6 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center text-white text-[10px] font-semibold ring-2 ring-white"
                                                                    title="{{ $user->name }}">
                                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                                </div>
                                                            @endforeach
                                                            @if ($task->assignedUsers->count() > 3)
                                                                <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-[10px] font-semibold ring-2 ring-white"
                                                                    title="+{{ $task->assignedUsers->count() - 3 }} más">
                                                                    +{{ $task->assignedUsers->count() - 3 }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400 italic text-xs">
                                                            <i class="far fa-user-circle mr-1"></i>No asignado
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Input crear nuevo bucket -->
                    <div x-data="{ creating: false }"
                        class="bg-white border border-dashed border-gray-300 rounded-lg p-3 flex flex-col justify-center items-center text-gray-500 min-w-[20rem]">

                        <!-- Botón inicial -->
                        <template x-if="!creating">
                            <button @click="creating = true" class="text-sm font-medium hover:text-yellow-600">
                                + Crear depósito
                            </button>
                        </template>

                        <!-- Input -->
                        <template x-if="creating">
                            <div class="w-full">
                                <x-input type="text" wire:model.defer="newBucketName" wire:keydown.enter="createBucket"
                                    class="w-full" placeholder="Nombre del depósito" x-ref="input"
                                    @keydown.escape="creating = false" @keydown.enter="creating = false" />
                                <div class="flex justify-end mt-2 gap-2">
                                    <button @click="creating = false"
                                        class="text-xs text-gray-500 hover:text-gray-700">Cancelar</button>
                                    <button wire:click="createBucket"
                                        class="text-xs text-yellow-600 font-semibold hover:text-yellow-700">Guardar</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- No plan selected state -->
    <template x-if="!activePlan">
        <div class="text-center py-12 bg-gray-50 rounded-lg">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-200 rounded-full mb-4">
                <i class="fas fa-hand-pointer text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Selecciona un plan</h3>
            <p class="text-gray-600">Elige uno de los planes disponibles arriba para ver sus buckets y tareas.</p>
        </div>
    </template>
</div>


@push('scripts-content')
    <script>
        (function () {
            if (typeof Sortable === 'undefined') {
                console.error('❌ SortableJS no está cargado.');
                return;
            }

            window.initBucketSorting = function (container, $wire) {
                if (!container) return;

                new Sortable(container, {
                    group: 'buckets',
                    animation: 150,
                    direction: 'horizontal',
                    forceFallback: true,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    onEnd(evt) {
                        const orderedIds = Array.from(container.children)
                            .map(el => el.dataset?.id)
                            .filter(Boolean);
                        $wire.call('reorderBuckets', orderedIds);
                    }
                });
            }

            window.initTaskSorting = function (container, $wire) {
                if (!container) return;

                new Sortable(container, {
                    group: 'tasks',
                    animation: 150,
                    forceFallback: true,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    onEnd(evt) {
                        const oldBucketId = evt.from?.dataset?.bucketId;
                        const newBucketId = evt.to?.dataset?.bucketId;
                        const orderedIds = Array.from(evt.to.children)
                            .map(el => el.dataset?.id)
                            .filter(Boolean);

                        if (oldBucketId == newBucketId && evt.oldIndex === evt.newIndex) return;

                        // Optimistic UI: Update task counts immediately if moving between buckets
                        if (oldBucketId != newBucketId) {
                            // Update source bucket count (decrement)
                            const sourceBucket = evt.from.closest('[data-id="' + oldBucketId + '"]');
                            if (sourceBucket) {
                                const sourceCountEl = sourceBucket.querySelector('.text-xs.text-gray-400');
                                if (sourceCountEl) {
                                    const currentCount = parseInt(sourceCountEl.textContent.trim()) || 0;
                                    sourceCountEl.textContent = Math.max(0, currentCount - 1);
                                }
                            }

                            // Update target bucket count (increment)
                            const targetBucket = evt.to.closest('[data-id="' + newBucketId + '"]');
                            if (targetBucket) {
                                const targetCountEl = targetBucket.querySelector('.text-xs.text-gray-400');
                                if (targetCountEl) {
                                    const currentCount = parseInt(targetCountEl.textContent.trim()) || 0;
                                    targetCountEl.textContent = currentCount + 1;
                                }
                            }
                        }

                        $wire.call('reorderTasks', oldBucketId, newBucketId, orderedIds);
                    }
                });
            }
        })();
    </script>
@endpush