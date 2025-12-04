<?php

namespace App\Livewire\Modules\Planner\Components\Tasks;

use App\Livewire\Forms\Modules\Planner\Tasks\Form;
use App\Models\Bucket;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Task;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CreateOrUpdate extends Component
{

    protected $listeners = [
        'open-create-task-modal' => 'openModal',
        'close-create-task-modal' => 'closeModal',
        'open-edit-task-modal' => 'openEditModal'
    ];

    public Form $form;

    public $isEdit = false;

    // public $showModalTask = false; // Removed


    public $states;
    public $priorities;

    public Bucket $bucket;

    public function mount()
    {

        $state_type = TagCategory::where('slug', 'estado_tarea')->first();
        $priority_type = TagCategory::where('slug', 'prioridad_tarea')->first();


        $this->priorities = Tag::where('category_id', $priority_type?->id)->get() ?? [];
        $this->states = Tag::where('category_id', $state_type?->id)->get() ?? [];
    }

    public function openModal($bucketId)
    {
        $this->form->resetForm();
        $this->bucket = Bucket::find($bucketId);

        $this->isEdit = false;
        // $this->showModalTask = true; // Removed
        $this->dispatch('task-loaded');
    }

    public function openEditModal($bucketId, $taskId)
    {
        $this->form->resetForm();
        $this->bucket = Bucket::find($bucketId);
        $this->isEdit = true;
        $this->form->setTask(Task::find($taskId));
        // $this->showModalTask = true; // Removed
        $this->dispatch('task-loaded');


    }

    public function closeModal()
    {
        // $this->showModalTask = false; // Removed
        $this->dispatch('close-task-modal'); // Added
    }

    public function save()
    {
        if ($this->isEdit) {
            $this->form->update();
        } else {
            $this->form->store($this->bucket->id);

        }
        $this->dispatch('refresh-buckets');
        $this->closeModal();
    }

    #[Computed]
    public function users()
    {
        // Load team relationship if not already loaded
        if ($this->bucket && $this->bucket->plan) {
            $plan = $this->bucket->plan;

            // If plan has team_id, load team members
            if ($plan->team_id) {
                // Load team with members if not already loaded
                if (!$plan->relationLoaded('team')) {
                    $plan->load('team.members');
                }

                return $plan->team?->members ?? [];
            }
        }

        return [];
    }

    public function render()
    {
        return view('livewire.modules.planner.components.tasks.create-or-update');
    }
}
