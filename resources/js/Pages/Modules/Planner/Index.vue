<script setup>
import { ref, watch, nextTick, onMounted, onUnmounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
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
    groupPlans: Array,
    personalPlans: Array,
    selectedPlan: Object,
    buckets: Array,
    teams: Array,
    priorities: Array,
    states: Array,
});

const tab = ref('group');
const activePlanId = ref(props.selectedPlan?.id || null);
const newBucketName = ref('');
const bucketContainers = ref({});
const bucketsContainerRef = ref(null);
const creatingBucket = ref(false);
const editingBucketId = ref(null);
const editingBucketName = ref('');
const isEditingPlan = ref(false);
const showingPlanModal = ref(false);

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
    project_id: '',
});

// ... (existing methods for Plan/Bucket) ...

const openCreatePlanModal = () => {
    isEditingPlan.value = false;
    planForm.reset();
    showingPlanModal.value = true;
};

const openEditPlanModal = () => {
    if (!props.selectedPlan) return;
    
    isEditingPlan.value = true;
    planForm.id = props.selectedPlan.id;
    planForm.name = props.selectedPlan.name;
    planForm.description = props.selectedPlan.description;
    planForm.team_id = props.selectedPlan.team_id || '';
    planForm.project_id = props.selectedPlan.project_id || '';
    showingPlanModal.value = true;
};

const savePlan = () => {
    if (isEditingPlan.value) {
        planForm.put(route('planner.update', planForm.id), {
            preserveScroll: true,
            onSuccess: () => {
                showingPlanModal.value = false;
                planForm.reset();
            }
        });
    } else {
        planForm.post(route('planner.store'), {
            preserveScroll: true,
            onSuccess: () => {
                showingPlanModal.value = false;
                planForm.reset();
            }
        });
    }
};


// Task Modal Logic
const openCreateTaskModal = (bucketId) => {
    isEditingTask.value = false;
    selectedBucketId.value = bucketId;
    taskForm.reset();
    taskForm.bucket_id = bucketId;
    // Set default status/priority if available? usually user selects.
    showingTaskModal.value = true;
};

const openEditTaskModal = (task) => {
    isEditingTask.value = true;
    taskForm.id = task.id;
    taskForm.bucket_id = task.bucket_id; // Usually shouldn't change bucket on edit here
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
    // Helpers
    const getStatusObj = (id) => props.states.find(s => s.id === id) || { name: '...', id };
    const getPriorityObj = (id) => props.priorities.find(p => p.id === id) || { name: '...', id };

    if (isEditingTask.value) {
        // Optimistic Update for Edit
        for (const bucket of localBuckets.value) {
            const task = bucket.tasks?.find(t => t.id === taskForm.id);
            if (task) {
                task.title = taskForm.title;
                task.notes = taskForm.notes;
                task.start_date = taskForm.start_date;
                task.due_date = taskForm.due_date;
                task.status_id = taskForm.status_id;
                task.priority_id = taskForm.priority_id;
                task.status = getStatusObj(taskForm.status_id);
                task.priority = getPriorityObj(taskForm.priority_id);
                
                // Optimistic assignment update
                if (props.selectedPlan?.team?.members) {
                     task.assigned_users = props.selectedPlan.team.members.filter(u => taskForm.assignedUsers.includes(u.id));
                }
                break;
            }
        }
        
        showingTaskModal.value = false;
        
        // If temp ID, don't update server yet (creation pending or just local)
        if (typeof taskForm.id === 'string' && taskForm.id.startsWith('temp-')) return;

        axios.put(route('planner.tasks.update', taskForm.id), taskForm.data())
            .catch(err => {
                console.error('Task update failed', err);
                router.reload();
            });

    } else {
        // Optimistic Creation
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

        // Add to local bucket
        const bucket = localBuckets.value.find(b => b.id === taskForm.bucket_id);
        if (bucket) {
             if (!bucket.tasks) bucket.tasks = [];
             bucket.tasks.push(newTask);
        }

        showingTaskModal.value = false;
        
        // Background Save
        // Using router.post instead of axios + reload to let Inertia handle the state transition naturally
        taskForm.post(route('planner.tasks.store'), {
            preserveScroll: true,
            onSuccess: () => {
                // Determine valid fields to reload to get real ID back?
                // Just reloading buckets is safest to get the real ID.
                // taskForm.reset(); // Don't reset here, handled by optimistic path for UI clear
            },
            onError: (err) => {
                 console.error('Task creation failed', err);
                 // Revert
                 if (bucket) {
                     const idx = bucket.tasks.findIndex(t => t.id === tempId);
                     if (idx !== -1) bucket.tasks.splice(idx, 1);
                 }
            },
             onFinish: () => {
                 // Clean up if needed
                 taskForm.reset();
             }
        });
    }
};

const deleteTask = (taskId) => {
    if (!confirm('¿Eliminar tarea?')) return;
    
    // Find task and bucket for optimistic removal
    let taskIndex = -1;
    let bucketIndex = -1;
    let removedTask = null;

    for (let i = 0; i < localBuckets.value.length; i++) {
        const tasks = localBuckets.value[i].tasks || [];
        const tIndex = tasks.findIndex(t => t.id === taskId);
        if (tIndex !== -1) {
            bucketIndex = i;
            taskIndex = tIndex;
            removedTask = tasks[tIndex];
            break;
        }
    }

    if (removedTask && bucketIndex !== -1) {
        // Optimistic Update
        localBuckets.value[bucketIndex].tasks.splice(taskIndex, 1);
        
        // Skip server call if temp ID
        if (typeof taskId === 'string' && taskId.startsWith('temp-')) return;

        // Background Sync
        axios.delete(route('planner.tasks.destroy', taskId))
            .catch(err => {
                console.error('Task deletion failed', err);
                // Revert
                if (localBuckets.value[bucketIndex]) {
                    localBuckets.value[bucketIndex].tasks.splice(taskIndex, 0, removedTask);
                }
            });
    }
};


// Switch tab
const changeTab = (newTab) => {
    tab.value = newTab;
    activePlanId.value = null;
    router.visit(route('planner.index'), {
        preserveState: true,
        preserveScroll: true,
        only: ['selectedPlan', 'buckets', 'team'],
        onSuccess: () => {}
    });
};

// ... (rest of logic: selectPlan, createBucket, etc) ... 

const selectPlan = (planId) => {
    activePlanId.value = planId;
    router.visit(route('planner.show', planId), {
        preserveState: true,
        preserveScroll: true,
        only: ['selectedPlan', 'buckets', 'team', 'states', 'priorities'],
    });
};

const createBucket = () => {
    const name = newBucketName.value.trim();
    if (!name) return;
    
    // Optimistic Update
    const tempId = 'temp-' + Date.now();
    const newBucket = {
        id: tempId,
        name: name,
        plan_id: activePlanId.value,
        tasks: [],
        items: [], // SortableJS might look for items
        order: localBuckets.value.length + 1
    };
    
    localBuckets.value.push(newBucket);
    newBucketName.value = '';
    creatingBucket.value = false;
    
    // Server Sync
    router.post(route('planner.buckets.store'), {
        name: name,
        plan_id: activePlanId.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // The prop watcher will sync the real ID
        },
        onError: () => {
            // Revert
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
        // Optimistic Update
        const bucket = localBuckets.value.find(b => b.id === editingBucketId.value);
        if (bucket) {
            const oldName = bucket.name;
            bucket.name = newName;
            
            // Background Sync
            axios.put(route('planner.buckets.update', editingBucketId.value), {
                name: newName
            }).catch(err => {
                console.error('Bucket update failed', err);
                bucket.name = oldName; // Revert
                // alert('Error updating bucket name');
            });
        }
    }
    editingBucketId.value = null;
};

const deleteBucket = (bucketId) => {
    if (!confirm('¿Estás seguro de eliminar este depósito?')) return;
    
    // Optimistic Update
    const bucketIndex = localBuckets.value.findIndex(b => b.id === bucketId);
    if (bucketIndex !== -1) {
        const removedBucket = localBuckets.value[bucketIndex];
        localBuckets.value.splice(bucketIndex, 1);
        
        // Check if it's a temp ID (string likely starting with temp-)
        // If so, don't send request to server as it doesn't exist there yet
        if (typeof bucketId === 'string' && bucketId.startsWith('temp-')) {
            return;
        }

        // Background Sync
        axios.delete(route('planner.buckets.destroy', bucketId))
            .catch(err => {
                console.error('Bucket deletion failed', err);
                localBuckets.value.splice(bucketIndex, 0, removedBucket); // Revert
            });
    }
};

const deletePlan = (planId) => {
    if (!confirm('¿Estás seguro de eliminar este plan? Toda la información se perderá.')) return;
    router.delete(route('planner.destroy', planId));
};



const localBuckets = ref([]);
const sortableInstances = ref([]);

// Sync props to local state


// Optimistic Sortable Logic
// Optimistic Sortable Logic
const initSortables = () => {
    // Cleanup existing instances
    sortableInstances.value.forEach(instance => instance.destroy());
    sortableInstances.value = [];

    // 1. Buckets Sorting
    if (bucketsContainerRef.value) {
        const bucketSortable = new Sortable(bucketsContainerRef.value, {
            group: 'buckets',
            animation: 150,
            direction: 'horizontal',
            handle: '.bucket-handle',
            draggable: '.bucket-item',
            forceFallback: true,
            fallbackOnBody: true,
            swapThreshold: 0.65,
            onEnd: (evt) => {
                if (evt.oldIndex === evt.newIndex) return;
                
                // Optimistic Update
                const movedItem = localBuckets.value.splice(evt.oldIndex, 1)[0];
                localBuckets.value.splice(evt.newIndex, 0, movedItem);

                const orderedIds = localBuckets.value.map(b => b.id);
                
                // Background Sync
                axios.post(route('planner.buckets.reorder'), {
                    orderedIds: orderedIds
                }).catch(err => {
                    console.error('Reorder failed', err);
                    router.reload();
                });
            }
        });
        sortableInstances.value.push(bucketSortable);
    }

    // 2. Tasks Sorting
    nextTick(() => {
        document.querySelectorAll('.tasks-container').forEach(el => {
             const taskSortable = new Sortable(el, {
                group: 'tasks',
                animation: 150,
                forceFallback: true,
                fallbackOnBody: true,
                swapThreshold: 0.65,
                onEnd: (evt) => {
                    const oldBucketId = parseInt(evt.from.dataset.bucketId);
                    const newBucketId = parseInt(evt.to.dataset.bucketId);
                    const oldIndex = evt.oldIndex;
                    const newIndex = evt.newIndex;
                    
                    if (oldBucketId === newBucketId && oldIndex === newIndex) return;

                    // Optimistic Update
                    const sourceBucket = localBuckets.value.find(b => b.id === oldBucketId);
                    const targetBucket = localBuckets.value.find(b => b.id === newBucketId);

                    if (sourceBucket && targetBucket) {
                        const task = sourceBucket.tasks.splice(oldIndex, 1)[0];
                        task.bucket_id = newBucketId; // Update local task property
                        targetBucket.tasks.splice(newIndex, 0, task);
                    }

                    // Calculate new order for target bucket
                    // Filter out temp IDs for the server request to avoid 422/500
                    const orderedIds = targetBucket.tasks
                        .map(t => t.id)
                        .filter(id => !(typeof id === 'string' && id.startsWith('temp-')));

                    // Background Sync
                    axios.post(route('planner.tasks.reorder'), {
                        oldBucketId: oldBucketId,
                        newBucketId: newBucketId,
                        orderedIds: orderedIds
                    }).catch(err => {
                        console.error('Task reorder failed', err);
                        router.reload();
                    });
                }
            });
            sortableInstances.value.push(taskSortable);
        });
    });
};

// Sync props to local state
watch(() => props.buckets, (newBuckets) => {
    localBuckets.value = JSON.parse(JSON.stringify(newBuckets));
    nextTick(initSortables);
}, { deep: true, immediate: true });

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
</script>

<template>
    <AppLayout title="Planificador">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Planificador
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    
                    <!-- TABS -->
                    <div class="flex justify-between gap-4 border-b border-gray-200 mb-4">
                        <div>
                            <button @click="changeTab('group')"
                                :class="tab === 'group' ? 'border-b-2 border-yellow-500 text-yellow-600' : 'text-gray-500'"
                                class="px-4 py-2 font-semibold transition-colors">
                                Otros planes/proyectos
                            </button>
                            <button @click="changeTab('personal')"
                                :class="tab === 'personal' ? 'border-b-2 border-yellow-500 text-yellow-600' : 'text-gray-500'"
                                class="px-4 py-2 font-semibold transition-colors">
                                Planes personales
                            </button>
                        </div>
                        <div>
                            <PrimaryButton @click="openCreatePlanModal">Crear Plan</PrimaryButton>
                        </div>
                    </div>

                    <!-- PLAN LIST -->
                    <div class="mb-4">
                        <div v-if="tab === 'group'" class="flex overflow-x-auto space-x-3 pb-2">
                             <template v-if="props.groupPlans.length">
                                <button v-for="plan in props.groupPlans" :key="plan.id"
                                    @click="selectPlan(plan.id)"
                                    :class="activePlanId === plan.id ? 'bg-yellow-500 text-white font-semibold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="whitespace-nowrap px-4 py-2 text-sm rounded-md transition-colors">
                                    {{ plan.name }}
                                </button>
                             </template>
                             <p v-else class="text-gray-500 text-sm italic px-4 py-2">No hay planes grupales.</p>
                        </div>
                        <div v-if="tab === 'personal'" class="flex overflow-x-auto space-x-3 pb-2">
                            <template v-if="props.personalPlans.length">
                                <button v-for="plan in props.personalPlans" :key="plan.id"
                                    @click="selectPlan(plan.id)"
                                    :class="activePlanId === plan.id ? 'bg-yellow-500 text-white font-semibold' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                    class="whitespace-nowrap px-4 py-2 text-sm rounded-md transition-colors">
                                    {{ plan.name }}
                                </button>
                             </template>
                             <p v-else class="text-gray-500 text-sm italic px-4 py-2">No hay planes personales.</p>
                        </div>
                    </div>

                    <!-- PLAN CONTENT -->
                    <div v-if="activePlanId && props.selectedPlan" class="animate-fade-in-down">
                        <!-- Toolbar -->
                        <div class="flex justify-between mb-4">
                            <div class="flex gap-2">
                                <SecondaryButton @click="openEditPlanModal">Editar plan</SecondaryButton>
                                <DangerButton @click="deletePlan(activePlanId)">Eliminar plan</DangerButton>
                            </div>
                        </div>

                        <!-- Buckets Container -->
                        <div class="relative w-full overflow-auto">
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
                                                <!-- Dropdown Menu (Simplified) -->
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
                                                
                                                <!-- Task Title -->
                                                <div class="flex justify-between items-start mb-2">
                                                    <p class="font-medium text-sm text-gray-900 flex-1 pr-2 line-clamp-2">
                                                        {{ task.title }}
                                                    </p>
                                                    <button @click.stop="deleteTask(task.id)" class="text-gray-400 hover:text-red-500 px-1">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>

                                                <!-- Bagdes -->
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

                                                <!-- Footer -->
                                                <div class="flex justify-between items-center text-xs pt-2 border-t border-gray-100">
                                                    <div v-if="task.due_date" class="flex items-center gap-1 text-gray-500">
                                                        <i class="far fa-calendar text-xs"></i>
                                                        <span>{{ new Date(task.due_date).toLocaleDateString('es-ES') }}</span>
                                                    </div>
                                                    <div v-else></div>

                                                    <!-- Assignees (Simplified) -->
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

                    <!-- Empty State -->
                    <div v-else class="text-center py-12 bg-gray-50 rounded-lg">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-200 rounded-full mb-4">
                            <i class="fas fa-hand-pointer text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Selecciona un plan</h3>
                        <p class="text-gray-600">Elige uno de los planes disponibles arriba para ver sus buckets y tareas.</p>
                    </div>

                </div>
            </div>
        </div>

        <!-- Plan Modal -->
        <DialogModal :show="showingPlanModal" @close="showingPlanModal = false">
            <template #title>
                {{ isEditingPlan ? 'Editar Plan' : 'Crear Plan' }}
            </template>
            <template #content>
                <div class="space-y-4">
                    <div>
                        <InputLabel for="name" value="Nombre" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="planForm.name" />
                        <InputError :message="planForm.errors.name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="description" value="Descripción" />
                        <textarea id="description" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm" v-model="planForm.description"></textarea>
                        <InputError :message="planForm.errors.description" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="team" value="Equipo (Opcional)" />
                        <select id="team" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm" v-model="planForm.team_id" :disabled="isEditingPlan">
                            <option value="">Seleccionar</option>
                            <option v-for="team in props.teams" :key="team.id" :value="team.id">{{ team.name }}</option>
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

        <!-- Task Modal -->
        <DialogModal :show="showingTaskModal" @close="showingTaskModal = false">
            <template #title>
                {{ isEditingTask ? 'Editar Tarea' : 'Crear Tarea' }}
            </template>
            <template #content>
                 <div class="space-y-4 max-h-[70vh] overflow-y-auto p-1">
                    <!-- Title -->
                    <div>
                        <InputLabel for="task_title" value="Titulo" />
                        <TextInput id="task_title" type="text" class="mt-1 block w-full" v-model="taskForm.title" placeholder="Usa un nombre que sea fácil de entender" />
                        <InputError :message="taskForm.errors.title" class="mt-2" />
                    </div>

                    <!-- Status -->
                    <div>
                        <InputLabel for="task_status" value="Estado" />
                        <select id="task_status" v-model="taskForm.status_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                            <option value="">Selecciona un estado</option>
                            <option v-for="state in props.states" :key="state.id" :value="state.id">{{ state.name }}</option>
                        </select>
                         <InputError :message="taskForm.errors.status_id" class="mt-2" />
                    </div>

                    <!-- Priority -->
                    <div>
                        <InputLabel for="task_priority" value="Prioridad" />
                        <select id="task_priority" v-model="taskForm.priority_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm">
                            <option value="">Selecciona una prioridad</option>
                            <option v-for="priority in props.priorities" :key="priority.id" :value="priority.id">{{ priority.name }}</option>
                        </select>
                         <InputError :message="taskForm.errors.priority_id" class="mt-2" />
                    </div>

                    <!-- Assigned Users -->
                    <div>
                        <InputLabel value="Asignado" />
                        <template v-if="props.selectedPlan?.team?.members?.length">
                            <select multiple v-model="taskForm.assignedUsers" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm min-h-[100px]">
                                <option v-for="user in props.selectedPlan.team.members" :key="user.id" :value="user.id">{{ user.name }}</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Puedes seleccionar múltiples usuarios usando Ctrl/Cmd+Click
                            </p>
                        </template>
                        <template v-else>
                             <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                                <p class="text-sm text-yellow-800">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    {{ props.selectedPlan?.team_id ? 'El equipo no tiene miembros asignados.' : 'Este es un plan personal o sin equipo. No se puede asignar usuarios.' }}
                                </p>
                            </div>
                        </template>
                         <InputError :message="taskForm.errors.assignedUsers" class="mt-2" />
                    </div>

                    <!-- Dates -->
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

                    <!-- Notes -->
                    <div>
                         <InputLabel for="task_notes" value="Notas" />
                        <textarea id="task_notes" rows="3" v-model="taskForm.notes" placeholder="Notas de la tarea" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-yellow-500 focus:ring-yellow-500 sm:text-sm"></textarea>
                         <InputError :message="taskForm.errors.notes" class="mt-2" />
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
    </AppLayout>
</template>

<style scoped>
.ghost {
    opacity: 0.5;
    background: #c8ebfb;
}
</style>
