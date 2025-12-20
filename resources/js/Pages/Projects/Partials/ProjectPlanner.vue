<script setup>
import { ref, watch, nextTick, onMounted, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Sortable from 'sortablejs';
import axios from 'axios';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    project: Object,
    selectedPlan: Object,
    buckets: {
        type: Array,
        default: () => []
    },
    teams: {
        type: Array,
        default: () => []
    },
    priorities: {
        type: Array,
        default: () => []
    },
    states: {
        type: Array,
        default: () => []
    },
});

const activePlanId = ref(props.selectedPlan?.id || null);
const newBucketName = ref('');
const bucketContainers = ref({});
const bucketsContainerRef = ref(null);
const ganttContainerRef = ref(null);
const creatingBucket = ref(false);
const editingBucketId = ref(null);
const editingBucketName = ref('');
const isEditingPlan = ref(false);
const showingPlanModal = ref(false);

const localBuckets = ref([]);
const sortableInstances = ref([]);

// Gantt State
import Gantt from 'frappe-gantt';
import '../../../../css/frappe-gantt.css';
const viewMode = ref('kanban'); // 'kanban' or 'gantt'
const ganttInstance = ref(null);
const ganttMode = ref('Month'); // Day, Week, Month
const ganttTasks = ref([]);

// Task Modal State
const showingTaskModal = ref(false);
const isEditingTask = ref(false);
const selectedBucketId = ref(null);
const taskForm = useForm({
    id: null,
    bucket_id: null,
    title: '',
    status_id: '',
    priority_id: '',
    notes: '',
    start_date: '',
    due_date: '',
    assignedUsers: [],
});

const planForm = useForm({
    id: null,
    name: '',
    description: '',
    team_id: '',
    project_id: props.project.id,
});

// --- Computed ---
const projectPlans = computed(() => props.project.plans || []);

// --- Methods ---

const selectPlan = (planId) => {
    activePlanId.value = planId;
    router.get(route('projects.show', { project: props.project.id, plan_id: planId }), {}, {
        preserveState: true,
        preserveScroll: true,
        only: ['selectedPlan', 'buckets'],
        onSuccess: () => {
             // Let watcher handle localBuckets update
        }
    });
};

const openCreatePlanModal = () => {
    isEditingPlan.value = false;
    planForm.reset();
    planForm.project_id = props.project.id;
    showingPlanModal.value = true;
};

const openEditPlanModal = () => {
    if (!props.selectedPlan) return;
    
    isEditingPlan.value = true;
    planForm.id = props.selectedPlan.id;
    planForm.name = props.selectedPlan.name;
    planForm.description = props.selectedPlan.description;
    planForm.team_id = props.selectedPlan.team_id || '';
    planForm.project_id = props.project.id; // Keep project context
    showingPlanModal.value = true;
};

const savePlan = () => {
    if (isEditingPlan.value) {
        planForm.put(route('planner.update', planForm.id), {
            preserveScroll: true,
            onSuccess: () => {
                showingPlanModal.value = false;
                planForm.reset();
                // Refresh project to get updated plans list if needed, usually auto-reloaded by inertia
            }
        });
    } else {
        planForm.post(route('planner.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showingPlanModal.value = false;
                planForm.reset();
                // We might need to select the new plan or refresh list
                router.reload({ only: ['project'] });
            }
        });
    }
};

const deletePlan = (planId) => {
    if (!confirm('¿Estás seguro de eliminar este plan? Toda la información se perderá.')) return;
    router.delete(route('planner.destroy', planId), {
        onSuccess: () => {
             activePlanId.value = null;
             // Redirect to project base show logic which handles "no plan selected"
             router.visit(route('projects.show', props.project.id));
        }
    });
};

// --- Buckets Logic ---

const createBucket = () => {
    const name = newBucketName.value.trim();
    if (!name) return;
    
    const tempId = 'temp-' + Date.now();
    const newBucket = {
        id: tempId,
        name: name,
        plan_id: activePlanId.value,
        tasks: [],
        order: localBuckets.value.length + 1
    };
    
    localBuckets.value.push(newBucket);
    newBucketName.value = '';
    creatingBucket.value = false;
    
    nextTick(initSortables); // Re-init specific bucket area if needed

    router.post(route('planner.buckets.store'), {
        name: name,
        plan_id: activePlanId.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {},
        onError: () => {
            const idx = localBuckets.value.findIndex(b => b.id === tempId);
            if (idx !== -1) localBuckets.value.splice(idx, 1);
        }
    });
};

const startEditBucket = (bucket) => {
    editingBucketId.value = bucket.id;
    editingBucketName.value = bucket.name;
    nextTick(() => {
        document.getElementById(`bucket-input-${bucket.id}`)?.focus();
    });
};

const saveBucketName = () => {
    if (!editingBucketId.value) return;
    const newName = editingBucketName.value.trim();
    
    if (newName !== '') {
        const bucket = localBuckets.value.find(b => b.id === editingBucketId.value);
        if (bucket) {
            const oldName = bucket.name;
            bucket.name = newName;
            
            axios.put(route('planner.buckets.update', editingBucketId.value), { name: newName })
                .catch(() => { bucket.name = oldName; });
        }
    }
    editingBucketId.value = null;
};

const deleteBucket = (bucketId) => {
    if (!confirm('¿Estás seguro de eliminar este depósito?')) return;
    
    const bucketIndex = localBuckets.value.findIndex(b => b.id === bucketId);
    if (bucketIndex !== -1) {
        const removedBucket = localBuckets.value[bucketIndex];
        localBuckets.value.splice(bucketIndex, 1);
        
        if (typeof bucketId === 'string' && bucketId.startsWith('temp-')) return;

        axios.delete(route('planner.buckets.destroy', bucketId))
            .catch(() => { localBuckets.value.splice(bucketIndex, 0, removedBucket); });
    }
};

// --- Tasks Logic ---
const openCreateTaskModal = (bucketId) => {
    isEditingTask.value = false;
    selectedBucketId.value = bucketId;
    taskForm.reset();
    taskForm.bucket_id = bucketId;
    // Set default status/priority (first available)
    if (props.states.length) taskForm.status_id = props.states[0].id;
    if (props.priorities.length) taskForm.priority_id = props.priorities[0].id;
    
    showingTaskModal.value = true;
};

const openEditTaskModal = (task) => {
    isEditingTask.value = true;
    taskForm.id = task.id;
    taskForm.bucket_id = task.bucket_id;
    taskForm.title = task.title;
    taskForm.status_id = task.status_id;
    taskForm.priority_id = task.priority_id;
    taskForm.notes = task.notes;
    taskForm.start_date = task.start_date ? task.start_date.substring(0, 10) : '';
    taskForm.due_date = task.due_date ? task.due_date.substring(0, 10) : '';
    taskForm.assignedUsers = task.assigned_users ? task.assigned_users.map(u => u.id) : [];
    showingTaskModal.value = true;
};

const saveTask = () => {
    const getStatusObj = (id) => props.states.find(s => s.id === id) || { name: '...', id };
    const getPriorityObj = (id) => props.priorities.find(p => p.id === id) || { name: '...', id };

    if (isEditingTask.value) {
        // Find task and update locally
        for (const bucket of localBuckets.value) {
            const task = bucket.tasks?.find(t => t.id === taskForm.id);
            if (task) {
                Object.assign(task, {
                    title: taskForm.title,
                    notes: taskForm.notes,
                    start_date: taskForm.start_date,
                    due_date: taskForm.due_date,
                    status_id: taskForm.status_id,
                    priority_id: taskForm.priority_id,
                    status: getStatusObj(taskForm.status_id),
                    priority: getPriorityObj(taskForm.priority_id),
                });
                
                // Optimistic assignment update
                 if (props.selectedPlan?.team?.members) {
                     task.assigned_users = props.selectedPlan.team.members.filter(u => taskForm.assignedUsers.includes(u.id));
                 }
                break;
            }
        }
        
        showingTaskModal.value = false;
        if (typeof taskForm.id === 'string' && taskForm.id.startsWith('temp-')) return;

        axios.put(route('planner.tasks.update', taskForm.id), taskForm.data())
            .catch(() => router.reload({ only: ['buckets'] }));

    } else {
        const tempId = 'temp-task-' + Date.now();
        const newTask = {
            id: tempId,
            bucket_id: taskForm.bucket_id,
            title: taskForm.title,
            notes: taskForm.notes,
            start_date: taskForm.start_date,
            due_date: taskForm.due_date,
            status: getStatusObj(taskForm.status_id),
            priority: getPriorityObj(taskForm.priority_id),
            priority_id: taskForm.priority_id,
            status_id: taskForm.status_id,
            assigned_users: [],
            order: 99999
        };
        
        if (props.selectedPlan?.team?.members) {
             newTask.assigned_users = props.selectedPlan.team.members.filter(u => taskForm.assignedUsers.includes(u.id));
        }

        const bucket = localBuckets.value.find(b => b.id === taskForm.bucket_id);
        if (bucket) {
             if (!bucket.tasks) bucket.tasks = [];
             bucket.tasks.push(newTask);
        }

        showingTaskModal.value = false;
        
        taskForm.post(route('planner.tasks.store'), {
            preserveScroll: true,
            onError: () => {
                 if (bucket) {
                     const idx = bucket.tasks.findIndex(t => t.id === tempId);
                     if (idx !== -1) bucket.tasks.splice(idx, 1);
                 }
            },
            onFinish: () => taskForm.reset()
        });
    }
};

const deleteTask = (taskId) => {
    if (!confirm('¿Eliminar tarea?')) return;
    
    let taskIndex = -1, bucketIndex = -1, removedTask = null;

    for (let i = 0; i < localBuckets.value.length; i++) {
        const tasks = localBuckets.value[i].tasks || [];
        const tIndex = tasks.findIndex(t => t.id === taskId);
        if (tIndex !== -1) {
            bucketIndex = i; taskIndex = tIndex; removedTask = tasks[tIndex]; break;
        }
    }

    if (removedTask && bucketIndex !== -1) {
        localBuckets.value[bucketIndex].tasks.splice(taskIndex, 1);
        if (typeof taskId === 'string' && taskId.startsWith('temp-')) return;

        axios.delete(route('planner.tasks.destroy', taskId))
            .catch(() => { localBuckets.value[bucketIndex].tasks.splice(taskIndex, 0, removedTask); });
    }
};

// --- SortableJS & Init ---

const initSortables = () => {
    sortableInstances.value.forEach(instance => instance.destroy());
    sortableInstances.value = [];

    // Buckets
    if (bucketsContainerRef.value) {
        const bucketSortable = new Sortable(bucketsContainerRef.value, {
            group: 'buckets',
            animation: 150,
            direction: 'horizontal',
            handle: '.bucket-handle',
            draggable: '.bucket-item',
            forceFallback: true, fallbackOnBody: true, swapThreshold: 0.65,
            onEnd: (evt) => {
                if (evt.oldIndex === evt.newIndex) return;
                const movedItem = localBuckets.value.splice(evt.oldIndex, 1)[0];
                localBuckets.value.splice(evt.newIndex, 0, movedItem);
                
                axios.post(route('planner.buckets.reorder'), { 
                    orderedIds: localBuckets.value.map(b => b.id).filter(id => !String(id).startsWith('temp-')) 
                })
                    .catch(() => router.reload({ only: ['buckets'] }));
            }
        });
        sortableInstances.value.push(bucketSortable);
    }

    // Tasks
    nextTick(() => {
        document.querySelectorAll('.tasks-container').forEach(el => {
             const taskSortable = new Sortable(el, {
                group: 'tasks',
                animation: 150,
                forceFallback: true, fallbackOnBody: true, swapThreshold: 0.65,
                onEnd: (evt) => {
                    const oldBucketId = parseInt(evt.from.dataset.bucketId);
                    const newBucketId = parseInt(evt.to.dataset.bucketId);
                    const oldIndex = evt.oldIndex;
                    const newIndex = evt.newIndex;
                    
                    if (oldBucketId === newBucketId && oldIndex === newIndex) return;

                    const sourceBucket = localBuckets.value.find(b => b.id === oldBucketId);
                    const targetBucket = localBuckets.value.find(b => b.id === newBucketId);

                    if (sourceBucket && targetBucket) {
                        const task = sourceBucket.tasks.splice(oldIndex, 1)[0];
                        task.bucket_id = newBucketId;
                        targetBucket.tasks.splice(newIndex, 0, task);
                    }

                    const orderedIds = targetBucket.tasks.map(t => t.id).filter(id => !(typeof id === 'string' && id.startsWith('temp-')));

                    axios.post(route('planner.tasks.reorder'), { oldBucketId, newBucketId, orderedIds })
                        .catch(() => router.reload({ only: ['buckets'] }));
                }
            });
            sortableInstances.value.push(taskSortable);
        });
    });
};

watch(() => props.buckets, (newBuckets) => {
    // Only update localBuckets from props if we're not currently dragging/interacting to avoid jitter
    // Ideally we diff, but deep clone + replace is standard given Inertia's full prop reload
    // We can add a simple check if we want, or just accept the update
    localBuckets.value = JSON.parse(JSON.stringify(newBuckets));
    nextTick(initSortables);
}, { deep: true, immediate: true });

// --- Helpers ---
const getStatusClasses = (statusName) => {
    const lower = statusName?.toLowerCase() || '';
    if (lower === 'completado' || lower === 'completada') return 'bg-green-100 text-green-800';
    if (lower === 'en progreso') return 'bg-blue-100 text-blue-800';
    if (lower === 'pendiente') return 'bg-yellow-100 text-yellow-800';
    return 'bg-gray-100 text-gray-800';
};

const getPriorityClasses = (priorityName) => {
    const lower = priorityName?.toLowerCase() || '';
    if (lower === 'alta' || lower === 'urgente') return 'bg-red-100 text-red-800';
    if (lower === 'media') return 'bg-orange-100 text-orange-800';
    if (lower === 'baja') return 'bg-gray-100 text-gray-600';
    return 'bg-purple-100 text-purple-800';
};

// --- Gantt Logic ---
const initGantt = () => {
    if (viewMode.value !== 'gantt') return;
    
    // Transform buckets/tasks to Gantt tasks
    const tasks = [];
    localBuckets.value.forEach(bucket => {
        if (bucket.tasks) {
            bucket.tasks.forEach(task => {
                const startDate = task.start_date ? task.start_date.substring(0, 10) : null;
                const endDate = task.due_date ? task.due_date.substring(0, 10) : null;
                
                if (startDate && endDate) {
                    // Map priority to a single safe class name for frappe-gantt
                    let priorityClass = 'gantt-priority-default';
                    const pName = task.priority?.name?.toLowerCase() || '';
                    if (pName === 'alta' || pName === 'urgente') priorityClass = 'gantt-priority-high';
                    else if (pName === 'media') priorityClass = 'gantt-priority-medium';
                    else if (pName === 'baja') priorityClass = 'gantt-priority-low';

                    tasks.push({
                        id: String(task.id),
                        name: task.title,
                        start: startDate,
                        end: endDate,
                        progress: task.progress || (task.status?.name?.toLowerCase() === 'completado' ? 100 : 0),
                        dependencies: '', 
                        custom_class: priorityClass, 
                    });
                }
            });
        }
    });

    nextTick(() => {
        if (!ganttContainerRef.value) return;
        
        ganttContainerRef.value.innerHTML = ''; // Clear previous

        if (tasks.length === 0) {
             ganttContainerRef.value.innerHTML = '<div class="flex flex-col items-center justify-center h-64 text-gray-400"><i class="far fa-calendar-times text-2xl mb-2"></i><p>No hay tareas con fechas asignadas para mostrar.</p></div>';
            return;
        }

        // Use setTimeout to allow v-show transition/rendering to complete fully so dimensions are correct
        setTimeout(() => {
            try {
                ganttInstance.value = new Gantt(ganttContainerRef.value, tasks, {
                    header_height: 50,
                    column_width: 30,
                    step: 24,
                    view_modes: ['Quarter Day', 'Half Day', 'Day', 'Week', 'Month'],
                    bar_height: 30,
                    bar_corner_radius: 4,
                    arrow_curve: 5,
                    padding: 22,
                    view_mode: ganttMode.value,
                    date_format: 'YYYY-MM-DD',
                    language: 'es', 
                    on_date_change: function(task, start, end) {
                        updateTaskDates(task.id, start, end);
                    },
                    on_view_change: function(mode) {
                       ganttMode.value = mode;
                    },
                });
            } catch (e) {
                // console.error("Gantt Init Error:", e);
                ganttContainerRef.value.innerHTML = '<p class="text-red-500 p-4">Error al cargar el gráfico Gantt. Revisa la consola.</p>';
            }
        }, 100);
    });
};

const changeGanttMode = (mode) => {
    ganttMode.value = mode;
    if (ganttInstance.value) {
        ganttInstance.value.change_view_mode(mode);
    }
};

const updateTaskDates = (taskId, start, end) => {
    // start and end are Date objects usually
    const formatDate = (d) => d.toISOString().substring(0, 10);
    const startDate = formatDate(start);
    const dueDate = formatDate(end);

    let foundTask = null;
    // Find task locally to update immediately
    for (const bucket of localBuckets.value) {
        const task = bucket.tasks?.find(t => String(t.id) === String(taskId));
        if (task) {
            task.start_date = startDate;
            task.due_date = dueDate;
            foundTask = task;
            break;
        }
    }

    if (!foundTask) return;

    // We must send ALL required fields because the controller valiates them as required
    // The controller validation says: title, status_id, priority_id are required.
    const payload = {
        title: foundTask.title,
        status_id: foundTask.status_id,
        priority_id: foundTask.priority_id,
        notes: foundTask.notes,
        assignedUsers: foundTask.assigned_users ? foundTask.assigned_users.map(u => u.id) : [],
        start_date: startDate,
        due_date: dueDate
    };

    axios.put(route('planner.tasks.update', taskId), payload).catch(err => {
        // console.error("Failed to update dates", err);
        router.reload({ only: ['buckets'] });
    });
};

watch(viewMode, (newMode) => {
    if (newMode === 'gantt') {
        initGantt();
    }
});

watch(() => props.buckets, () => {
    if (viewMode.value === 'gantt') {
        initGantt();
    }
}, { deep: true });
</script>

<template>
    <div>
        <!-- Plan Selection Tabs -->
        <div class="mb-4">
            <div class="flex overflow-x-auto space-x-3 pb-2 items-center">
                <template v-if="projectPlans.length">
                    <button v-for="plan in projectPlans" :key="plan.id"
                        @click="selectPlan(plan.id)"
                        :class="activePlanId == plan.id ? 'bg-yellow-500 text-white font-semibold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                        class="whitespace-nowrap px-4 py-2 text-sm rounded-md transition-colors"
                    >
                        {{ plan.name }}
                    </button>
                    <!-- Separator -->
                    <div class="h-6 w-px bg-gray-300 mx-1"></div>
                </template>
                <div v-else class="text-gray-500 text-sm italic px-2">No hay planes.</div>
                
                <button @click="openCreatePlanModal" class="px-3 py-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-md text-sm font-medium transition-colors border border-indigo-200">
                    <i class="fas fa-plus mr-1"></i>Nuevo Plan
                </button>
            </div>
        </div>

        <!-- Selected Plan View -->
        <div v-if="activePlanId && selectedPlan" class="animate-fade-in-down">
            <!-- Gantt Chart -->
            <div v-show="viewMode === 'gantt'" class="w-full max-w-full bg-white rounded-lg shadow-sm border border-gray-200 min-h-[500px] p-2">
                 <div class="w-full overflow-auto" style="max-width: 100%;">
                     <div ref="ganttContainerRef" class="gantt-target w-full"></div>
                 </div>
            </div>         <!-- Toolbar -->
            <div class="flex justify-between mb-4 bg-gray-50 p-2 rounded-lg border border-gray-100 flex-wrap gap-2">
                 <div class="flex items-center gap-2">
                     <span class="text-sm font-medium text-gray-500">Plan:</span>
                     <span class="text-sm font-bold text-gray-800">{{ selectedPlan.name }}</span>
                 </div>
                 
                <div class="flex items-center gap-2">
                     <a v-if="selectedPlan" 
                        :href="route('projects.plans.export-gantt', { project: project.id, plan: selectedPlan.id })" 
                        target="_blank"
                        class="px-3 py-1 bg-white border border-gray-200 text-gray-700 rounded-md hover:bg-gray-50 flex items-center text-xs font-medium transition-colors mr-2">
                         <i class="fas fa-file-pdf mr-2 text-red-500"></i> Exportar PDF
                     </a>
                    <!-- View Toggles -->
                    <div class="bg-gray-200 p-1 rounded-md flex text-xs font-medium">
                        <button @click="viewMode = 'kanban'" :class="viewMode === 'kanban' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1 rounded transition-all">
                            <i class="fas fa-columns mr-1"></i> Tablero
                        </button>
                        <button @click="viewMode = 'gantt'" :class="viewMode === 'gantt' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-3 py-1 rounded transition-all">
                            <i class="fas fa-stream mr-1"></i> Gantt
                        </button>
                    </div>

                    <template v-if="viewMode === 'gantt'">
                         <div class="h-4 w-px bg-gray-300 mx-1"></div>
                         <div class="bg-gray-200 p-1 rounded-md flex text-xs font-medium">
                            <button @click="changeGanttMode('Day')" :class="ganttMode === 'Day' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-2 py-1 rounded transition-all">Día</button>
                            <button @click="changeGanttMode('Week')" :class="ganttMode === 'Week' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-2 py-1 rounded transition-all">Semana</button>
                            <button @click="changeGanttMode('Month')" :class="ganttMode === 'Month' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'" class="px-2 py-1 rounded transition-all">Mes</button>
                        </div>
                    </template>

                    <div class="h-4 w-px bg-gray-300 mx-1"></div>

                    <button @click="openEditPlanModal" class="text-gray-500 hover:text-indigo-600 px-2 py-1 text-sm transition-colors" title="Editar plan">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button @click="deletePlan(activePlanId)" class="text-gray-500 hover:text-red-600 px-2 py-1 text-sm transition-colors" title="Eliminar plan">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>

            <!-- Kanban Board -->
            <!-- Kanban Board -->
             <div v-show="viewMode === 'kanban'" class="relative w-full overflow-auto">
                <div class="flex gap-4 min-w-max items-start pb-4">
                    
                    <!-- Buckets List -->
                    <div ref="bucketsContainerRef" class="flex gap-4 min-w-max items-start">
                        <div v-for="bucket in localBuckets" :key="bucket.id" :data-id="bucket.id"
                            class="bucket-item bg-gray-50 rounded-lg shadow-sm p-3 border border-gray-200 min-w-[20rem]">
                            
                            <!-- Bucket Header -->
                            <div class="flex justify-between items-center mb-2 handle cursor-move bucket-handle">
                                <div class="flex-1 mr-2">
                                     <h3 v-if="editingBucketId !== bucket.id" 
                                        @dblclick="startEditBucket(bucket)"
                                        class="font-semibold text-sm cursor-pointer hover:text-yellow-600 transition-colors"
                                        title="Doble click para editar">
                                        {{ bucket.name }}
                                    </h3>
                                    <input v-else
                                        :id="`bucket-input-${bucket.id}`"
                                        v-model="editingBucketName"
                                        @blur="saveBucketName"
                                        @keydown.enter="saveBucketName"
                                        @keydown.escape="editingBucketId = null"
                                        type="text"
                                        class="w-full text-sm font-semibold border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring-yellow-500 py-0 px-1 h-6">
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-400">{{ bucket.tasks?.length || 0 }}</span>
                                    <button @click="deleteBucket(bucket.id)" class="text-gray-400 hover:text-red-500">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Create Task Button -->
                            <button @click="openCreateTaskModal(bucket.id)" class="w-full text-center hover:bg-gray-100 py-2 mb-2 rounded-md text-gray-500 text-sm">
                                + Crear tarea
                            </button>

                            <!-- Tasks Container -->
                            <div :data-bucket-id="bucket.id" class="tasks-container min-h-[2rem]">
                                <div v-for="task in bucket.tasks" :key="task.id" :data-id="task.id"
                                    @click="openEditTaskModal(task)"
                                    class="transform transition-transform bg-white border border-gray-200 rounded-md p-3 mb-2 shadow-sm hover:shadow-md cursor-pointer">
                                    
                                    <div class="flex justify-between items-start mb-2">
                                        <p class="font-medium text-sm text-gray-900 flex-1 pr-2 line-clamp-2">
                                            {{ task.title }}
                                        </p>
                                        <button @click.stop="deleteTask(task.id)" class="text-gray-400 hover:text-red-500 px-1">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>

                                    <div class="flex flex-wrap gap-2 mb-2">
                                        <span v-if="task.status" :class="getStatusClasses(task.status.name)" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium">
                                            <i class="fas fa-circle text-[6px] mr-1.5"></i>
                                            {{ task.status.name }}
                                        </span>
                                        <span v-if="task.priority" :class="getPriorityClasses(task.priority.name)" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium">
                                            <i class="fas fa-flag text-[8px] mr-1.5"></i>
                                            {{ task.priority.name }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between items-center text-xs pt-2 border-t border-gray-100">
                                        <div v-if="task.due_date" class="flex items-center gap-1 text-gray-500">
                                            <i class="far fa-calendar text-xs"></i>
                                            <span>{{ new Date(task.due_date).toLocaleDateString('es-ES') }}</span>
                                        </div>
                                        <div v-else></div>

                                        <div class="flex items-center -space-x-2">
                                            <div v-for="user in (task.assigned_users || []).slice(0,3)" :key="user.id"
                                                class="w-6 h-6 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center text-white text-[10px] font-semibold ring-2 ring-white"
                                                :title="user.name">
                                                {{ user.name.substring(0,2).toUpperCase() }}
                                            </div>
                                            <div v-if="(task.assigned_users || []).length > 3" class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 text-[10px] font-semibold ring-2 ring-white">
                                                +{{ (task.assigned_users || []).length - 3 }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- New Bucket Form -->
                    <div class="bg-white border border-dashed border-gray-300 rounded-lg p-3 flex flex-col justify-center items-center text-gray-500 min-w-[20rem]">
                        <template v-if="!creatingBucket">
                            <button @click="creatingBucket = true" class="text-sm font-medium hover:text-yellow-600 w-full h-full py-2">
                                + Crear depósito
                            </button>
                        </template>
                        <template v-else>
                            <div class="w-full">
                                <TextInput v-model="newBucketName" @keydown.enter="createBucket" @keydown.escape="creatingBucket = false" placeholder="Nombre del depósito" class="w-full text-sm" />
                                <div class="flex justify-end mt-2 gap-2">
                                    <button @click="creatingBucket = false" class="text-xs text-gray-500 hover:text-gray-700">Cancelar</button>
                                    <button @click="createBucket" class="text-xs text-yellow-600 font-semibold hover:text-yellow-700">Guardar</button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <div v-else class="text-center py-12 bg-gray-50 rounded-lg">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-200 rounded-full mb-4">
                <i class="fas fa-hand-pointer text-gray-400 text-2xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Selecciona un plan</h3>
            <p class="text-gray-600">Elige uno de los planes disponibles arriba para ver sus depósitos y tareas.</p>
             <button @click="openCreatePlanModal" class="mt-4 px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition-colors">
                Crear primer plan
            </button>
        </div>

        <!-- Modals -->
        <DialogModal :show="showingPlanModal" @close="showingPlanModal = false">
            <template #title>
                {{ isEditingPlan ? 'Editar Plan' : 'Crear Plan' }}
            </template>
            <template #content>
                <div class="space-y-4">
                    <div>
                        <InputLabel for="plan_name" value="Nombre" />
                        <TextInput id="plan_name" type="text" class="mt-1 block w-full" v-model="planForm.name" />
                        <InputError :message="planForm.errors.name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="plan_desc" value="Descripción" />
                        <textarea id="plan_desc" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm" v-model="planForm.description"></textarea>
                        <InputError :message="planForm.errors.description" class="mt-2" />
                    </div>
                     <div>
                        <InputLabel for="plan_team" value="Equipo (Opcional)" />
                        <select id="plan_team" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm" v-model="planForm.team_id" :disabled="isEditingPlan">
                            <option value="">Seleccionar</option>
                            <option v-for="team in teams" :key="team.id" :value="team.id">{{ team.name }}</option>
                        </select>
                        <InputError :message="planForm.errors.team_id" class="mt-2" />
                         <p v-if="isEditingPlan" class="text-xs text-gray-500 mt-1">El equipo no se puede cambiar después de crear el plan.</p>
                    </div>
                </div>
            </template>
            <template #footer>
                <SecondaryButton @click="showingPlanModal = false" class="mr-2">Cancelar</SecondaryButton>
                <PrimaryButton @click="savePlan" :disabled="planForm.processing">
                    {{ isEditingPlan ? 'Actualizar' : 'Crear' }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <DialogModal :show="showingTaskModal" @close="showingTaskModal = false">
             <template #title>
                {{ isEditingTask ? 'Editar Tarea' : 'Crear Tarea' }}
            </template>
            <template #content>
                 <div class="space-y-4 max-h-[70vh] overflow-y-auto p-1">
                    <div>
                        <InputLabel for="task_title" value="Titulo" />
                        <TextInput id="task_title" type="text" class="mt-1 block w-full" v-model="taskForm.title" placeholder="Titulo de la tarea" />
                        <InputError :message="taskForm.errors.title" class="mt-2" />
                    </div>
                    <div>
                         <InputLabel for="task_status" value="Estado" />
                        <select id="task_status" v-model="taskForm.status_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                            <option value="">Selecciona un estado</option>
                            <option v-for="state in states" :key="state.id" :value="state.id">{{ state.name }}</option>
                        </select>
                         <InputError :message="taskForm.errors.status_id" class="mt-2" />
                    </div>
                     <div>
                        <InputLabel for="task_priority" value="Prioridad" />
                        <select id="task_priority" v-model="taskForm.priority_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                            <option value="">Selecciona una prioridad</option>
                            <option v-for="priority in priorities" :key="priority.id" :value="priority.id">{{ priority.name }}</option>
                        </select>
                         <InputError :message="taskForm.errors.priority_id" class="mt-2" />
                    </div>
                     <div>
                        <InputLabel value="Asignado" />
                        <template v-if="selectedPlan?.team?.members?.length">
                            <select multiple v-model="taskForm.assignedUsers" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm min-h-[100px]">
                                <option v-for="user in selectedPlan.team.members" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Ctrl/Cmd+Click para múltiples</p>
                        </template>
                        <template v-else>
                             <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                                <p class="text-sm text-yellow-800">Este plan no tiene un equipo asignado con miembros.</p>
                            </div>
                        </template>
                         <InputError :message="taskForm.errors.assignedUsers" class="mt-2" />
                    </div>
                     <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="start_date" value="Fecha de Inicio" />
                            <TextInput id="start_date" type="date" class="mt-1 block w-full" v-model="taskForm.start_date" />
                             <InputError :message="taskForm.errors.start_date" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="due_date" value="Fecha de Vencimiento" />
                            <TextInput id="due_date" type="date" class="mt-1 block w-full" v-model="taskForm.due_date" />
                             <InputError :message="taskForm.errors.due_date" class="mt-2" />
                        </div>
                    </div>
                 </div>
            </template>
            <template #footer>
                 <SecondaryButton @click="showingTaskModal = false" class="mr-2">Cancelar</SecondaryButton>
                <PrimaryButton @click="saveTask" :disabled="taskForm.processing">
                    {{ isEditingTask ? 'Actualizar' : 'Crear' }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </div>
</template>
