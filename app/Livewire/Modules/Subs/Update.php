<?php

namespace App\Livewire\Modules\Subs;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Livewire\Forms\Modules\Subs\Form;
use App\Models\Sub;
use App\Models\Tag;
use App\Models\TagCategory;

class Update extends Component
{
    use WithFileUploads;

    public Form $form;
    public Sub $sub;


    public function mount(Sub $sub)
    {
        $this->sub = $sub;
        $this->form->setSub($sub);
    }

    public function save()
    {

        $this->form->update();

        session()->flash('success', 'Suscripción actualizada exitosamente.');

        return redirect()->route('subs.index');
    }

    public function render()
    {
        $statusCategory = TagCategory::where('slug', 'estado_suscripcion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        $frequencyCategory = TagCategory::where('slug', 'frecuencia_sub')->first();
        $frequencyOptions = $frequencyCategory ? Tag::where('category_id', $frequencyCategory->id)->get() : collect();

        return view('livewire.modules.subs.create', [
            'statusOptions' => $statusOptions,
            'frequencyOptions' => $frequencyOptions,
            'isEdit' => true,
        ]);
    }
}
