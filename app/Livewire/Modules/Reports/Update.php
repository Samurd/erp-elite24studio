<?php

namespace App\Livewire\Modules\Reports;

use Livewire\Component;
use App\Livewire\Forms\Modules\Reports\Form;
use App\Models\Report;
use App\Models\Tag;
use App\Models\TagCategory;

class Update extends Component
{
    public Form $form;
    public Report $report;

    public function mount(Report $report)
    {
        $this->report = $report;
        $this->form->setReport($report);
    }

    public function save()
    {
        $this->form->update();

        session()->flash('success', 'Reporte actualizado exitosamente.');

        return redirect()->route('reports.index');
    }

    public function render()
    {
        $statusCategory = TagCategory::where('slug', 'estado_reporte')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.reports.create', [
            'statusOptions' => $statusOptions,
        ]);
    }
}
