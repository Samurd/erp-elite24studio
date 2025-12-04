<?php

namespace App\Livewire\Forms\Modules\Worksites\PunchItems;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\PunchItem;
use App\Models\Worksite;

class Form extends LivewireForm
{
    public $worksite_id;
    public $status_id;
    public $observations;
    public $responsible_id;

    protected $rules = [
        'worksite_id' => 'required|exists:worksites,id',
        'status_id' => 'nullable|exists:tags,id',
        'observations' => 'required|string',
        'responsible_id' => 'nullable|exists:users,id',
    ];

    public function store()
    {
        $this->validate();

        $punchItem = PunchItem::create([
            'worksite_id' => $this->worksite_id,
            'status_id' => $this->status_id,
            'observations' => $this->observations,
            'responsible_id' => $this->responsible_id,
        ]);

        session()->flash('success', 'Punch Item creado exitosamente.');

        return redirect()->route('worksites.show', $this->worksite_id);
    }

    public function update(PunchItem $punchItem)
    {
        $this->validate();

        $punchItem->update([
            'worksite_id' => $this->worksite_id,
            'status_id' => $this->status_id,
            'observations' => $this->observations,
            'responsible_id' => $this->responsible_id,
        ]);

        session()->flash('success', 'Punch Item actualizado exitosamente.');

        return redirect()->route('worksites.show', $this->worksite_id);
    }

    public function setPunchItem(PunchItem $punchItem)
    {
        $this->worksite_id = $punchItem->worksite_id;
        $this->status_id = $punchItem->status_id;
        $this->observations = $punchItem->observations;
        $this->responsible_id = $punchItem->responsible_id;
    }

    public function setWorksite(Worksite $worksite)
    {
        $this->worksite_id = $worksite->id;
    }
}
