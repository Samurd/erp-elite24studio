<?php

namespace App\Livewire\Forms\Modules\Approvals;

use App\Models\Approval;
use App\Models\Approver;
use App\Models\File;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Form as LivewireForm;

class Form extends LivewireForm
{

    public ?Approval $approval = null;

    #[Validate('required|string')]
    public $name;

    // Is it a purchase request?
    #[Validate('boolean')]
    public $buy = false;

    #[Validate('nullable|array')]
    public $approvers = []; // IDs de los usuarios seleccionados

    // Request one answer from all approvers
    #[Validate('boolean')]
    public $all_approvers = false;

    #[Validate('nullable|string')]
    public $description;

    #[Validate('nullable|integer')]
    public $priority_id;

    #[Validate(['nullable', 'array'])]
    #[Validate(['files.*' => 'file|max:2048'])]
    public $files = [];

    public function setApproval(Approval $approval)
    {
        $this->approval = $approval;
        $this->name = $approval->name;
        $this->buy = $approval->buy;
        $this->description = $approval->description;
        $this->priority_id = $approval->priority_id;
        $this->all_approvers = $approval->all_approvers;
        $this->approvers = $approval->approvers->pluck('user_id')->toArray();
    }

    public function store()
    {
        $this->validate();

        $state_type = TagCategory::where('slug', "estado_aprobacion")->first();

        $statePending = Tag::where('category_id', $state_type->id)->where('name', "En espera")->first();

        $approval = Approval::create([
            'name' => $this->name,
            'buy' => $this->buy,
            'description' => $this->description,
            'status_id' => $statePending->id,
            'priority_id' => $this->priority_id,
            'created_by_id' => Auth::user()->id,
            'all_approvers' => $this->all_approvers,
        ]);

        foreach ($this->approvers as $index => $userId) {
            Approver::create([
                'approval_id' => $approval->id,
                'user_id' => $userId,
                // 'order' => $this->order_approver ? $index + 1 : null,
                'status_id' => $statePending->id,
            ]);
        }

        return $approval;
    }

    public function update()
    {
        $this->validate();

        $this->approval->update([
            'name' => $this->name,
            'buy' => $this->buy,
            'description' => $this->description,
            'priority_id' => $this->priority_id,
            'all_approvers' => $this->all_approvers,
        ]);


        // Sync approvers (simplified: detach all and re-attach)
        // Note: This resets status of approvers. You might want to keep existing statuses if approver didn't change.
        // For now, simpler approach: sync

        $state_type = TagCategory::where('slug', "estado_aprobacion")->first();
        $statePending = Tag::where('category_id', $state_type->id)->where('name', "En espera")->first();

        // Get current approvers to avoid resetting status if possible, or just sync
        // If we use sync, we lose the status. 
        // Let's just add new ones and remove unselected ones.

        $currentApproverIds = $this->approval->approvers->pluck('user_id')->toArray();
        $newApproverIds = $this->approvers;

        // To delete
        $toDelete = array_diff($currentApproverIds, $newApproverIds);
        Approver::where('approval_id', $this->approval->id)->whereIn('user_id', $toDelete)->delete();

        // To add
        $toAdd = array_diff($newApproverIds, $currentApproverIds);
        foreach ($toAdd as $userId) {
            Approver::create([
                'approval_id' => $this->approval->id,
                'user_id' => $userId,
                'status_id' => $statePending->id,
            ]);
        }

        return $this->approval;
    }

}
