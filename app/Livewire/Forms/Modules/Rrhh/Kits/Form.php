<?php

namespace App\Livewire\Forms\Modules\Rrhh\Kits;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Kit;

class Form extends LivewireForm
{
    public ?Kit $kit = null;

    #[Validate('nullable|exists:users,id')]
    public $requested_by_user_id = null;

    #[Validate('required|string|max:255')]
    public $position_area = '';

    #[Validate('required|string|max:255')]
    public $recipient_name = '';

    #[Validate('required|string|max:255')]
    public $recipient_role = '';

    #[Validate('nullable|string|max:255')]
    public $kit_type = '';

    #[Validate('nullable|string')]
    public $kit_contents = '';

    #[Validate('required|date')]
    public $request_date = '';

    #[Validate('nullable|date')]
    public $delivery_date = '';

    #[Validate('nullable|exists:tags,id')]
    public $status_id = null;

    #[Validate('nullable|exists:users,id')]
    public $delivery_responsible_user_id = null;

    #[Validate('nullable|string')]
    public $observations = '';

    public function setKit(Kit $kit)
    {
        $this->kit = $kit;
        $this->requested_by_user_id = $kit->requested_by_user_id;
        $this->position_area = $kit->position_area;
        $this->recipient_name = $kit->recipient_name;
        $this->recipient_role = $kit->recipient_role;
        $this->kit_type = $kit->kit_type;
        $this->kit_contents = $kit->kit_contents;
        $this->request_date = $kit->request_date ? $kit->request_date->format('Y-m-d') : '';
        $this->delivery_date = $kit->delivery_date ? $kit->delivery_date->format('Y-m-d') : '';
        $this->status_id = $kit->status_id;
        $this->delivery_responsible_user_id = $kit->delivery_responsible_user_id;
        $this->observations = $kit->observations;
    }

    public function store()
    {
        $this->validate();

        Kit::create([
            'requested_by_user_id' => $this->requested_by_user_id ?? auth()->id(),
            'position_area' => $this->position_area,
            'recipient_name' => $this->recipient_name,
            'recipient_role' => $this->recipient_role,
            'kit_type' => $this->kit_type,
            'kit_contents' => $this->kit_contents,
            'request_date' => $this->request_date,
            'delivery_date' => $this->delivery_date,
            'status_id' => $this->status_id,
            'delivery_responsible_user_id' => $this->delivery_responsible_user_id,
            'observations' => $this->observations,
        ]);
    }

    public function update()
    {
        $this->validate();

        $this->kit->update([
            'requested_by_user_id' => $this->requested_by_user_id,
            'position_area' => $this->position_area,
            'recipient_name' => $this->recipient_name,
            'recipient_role' => $this->recipient_role,
            'kit_type' => $this->kit_type,
            'kit_contents' => $this->kit_contents,
            'request_date' => $this->request_date,
            'delivery_date' => $this->delivery_date,
            'status_id' => $this->status_id,
            'delivery_responsible_user_id' => $this->delivery_responsible_user_id,
            'observations' => $this->observations,
        ]);
    }
}
