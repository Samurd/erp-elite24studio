<?php

namespace App\Livewire\Forms\Modules\Marketing\AdPieces;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Adpiece;
use App\Models\Tag;
use App\Models\TagCategory;
use Illuminate\Support\Facades\Auth;

class Form extends LivewireForm
{
    public $type_id;
    public $format_id;
    public $status_id;
    public $project_id;
    public $team_id;
    public $strategy_id;
    public $name;
    public $media;
    public $instructions;

    protected $rules = [
        'type_id' => 'nullable|exists:tags,id',
        'format_id' => 'nullable|exists:tags,id',
        'status_id' => 'nullable|exists:tags,id',
        'project_id' => 'nullable|exists:projects,id',
        'team_id' => 'nullable|exists:teams,id',
        'strategy_id' => 'nullable|exists:strategies,id',
        'name' => 'required|string|max:255',
        'media' => 'nullable|string|max:255',
        'instructions' => 'nullable|string',
    ];

    public function store()
    {
        $this->validate();

        $adpiece = Adpiece::create([
            'type_id' => $this->type_id,
            'format_id' => $this->format_id,
            'status_id' => $this->status_id,
            'project_id' => $this->project_id,
            'team_id' => $this->team_id,
            'strategy_id' => $this->strategy_id,
            'name' => $this->name,
            'media' => $this->media,
            'instructions' => $this->instructions,
        ]);

        return $adpiece;
    }

    public function update(Adpiece $adpiece)
    {
        $this->validate();

        $adpiece->update([
            'type_id' => $this->type_id,
            'format_id' => $this->format_id,
            'status_id' => $this->status_id,
            'project_id' => $this->project_id,
            'team_id' => $this->team_id,
            'strategy_id' => $this->strategy_id,
            'name' => $this->name,
            'media' => $this->media,
            'instructions' => $this->instructions,
        ]);

        session()->flash('success', 'Pieza publicitaria actualizada exitosamente.');

        return redirect()->route('marketing.ad-pieces.index');
    }

    public function setAdpiece(Adpiece $adpiece)
    {
        $this->type_id = $adpiece->type_id;
        $this->format_id = $adpiece->format_id;
        $this->status_id = $adpiece->status_id;
        $this->project_id = $adpiece->project_id;
        $this->team_id = $adpiece->team_id;
        $this->strategy_id = $adpiece->strategy_id;
        $this->name = $adpiece->name;
        $this->media = $adpiece->media;
        $this->instructions = $adpiece->instructions;
    }
}
