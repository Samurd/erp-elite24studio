<?php

namespace App\Livewire\Modules\Taxes;

use Livewire\Component;
use App\Livewire\Forms\Modules\Taxes\Form;
use App\Models\TaxRecord;
use App\Models\Tag;
use App\Models\TagCategory;

class Update extends Component
{
    use \Livewire\WithFileUploads;

    public Form $form;
    public TaxRecord $taxRecord;


    public function mount(TaxRecord $taxRecord)
    {
        $this->taxRecord = $taxRecord;
        $this->form->setTaxRecord($taxRecord);
    }



    public function save()
    {

        $this->form->update();

        session()->flash('success', 'Impuesto actualizado exitosamente.');

        return redirect()->route('finances.taxes.index');
    }

    public function render()
    {
        $typeCategory = TagCategory::where('slug', 'tipo_impuesto')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_impuesto')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.taxes.create', [
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
        ]);
    }
}
