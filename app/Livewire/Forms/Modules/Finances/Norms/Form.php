<?php

namespace App\Livewire\Forms\Modules\Finances\Norms;

use App\Models\Norm;
use App\Services\FileUploadManager;
use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;

class Form extends LivewireForm
{
    public ?Norm $norm = null;

    #[Validate('required|string|max:255')]
    public $name = '';


    public function setNorm(Norm $norm)
    {
        $this->norm = $norm;
        $this->name = $norm->name;
    }

    public function store()
    {
        $this->validate();

        $norm = Norm::create([
            'name' => $this->name,
            'user_id' => auth()->id(),
        ]);

        $this->reset();

        return $norm;
    }

    public function update()
    {
        $this->validate();

        $this->norm->update([
            'name' => $this->name,
            'user_id' => auth()->id(),
        ]);

        $this->reset();
    }

}
