<?php

namespace App\Livewire\Modules\Rrhh\Vacancies;

use App\Livewire\Forms\Modules\Rrhh\Vacancies\Form;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Vacancy;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    
    public Vacancy $vacancy;

    public function mount(Vacancy $vacancy)
    {
        $this->vacancy = $vacancy;
        $this->form->setVacancy($vacancy);
    }

    public function save()
    {
        $this->form->validate();

        $this->vacancy->update([
            'title' => $this->form->title,
            'area' => $this->form->area,
            'contract_type_id' => $this->form->contract_type_id,
            'published_at' => $this->form->published_at ? $this->form->published_at : null,
            'status_id' => $this->form->status_id,
            'user_id' => $this->form->user_id,
            'description' => $this->form->description,
        ]);

        session()->flash('message', 'Vacante actualizada exitosamente.');

        return redirect()->route('rrhh.vacancies.index');
    }

    public function cancel()
    {
        return redirect()->route('rrhh.vacancies.index');
    }

    public function render()
    {
        // Obtener opciones para los selects usando TagCategory
        $contractCategory = TagCategory::where('slug', 'tipo_contrato')->first();
        $contractTypes = $contractCategory ? Tag::where('category_id', $contractCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_vacante')->first();
        $statuses = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.rrhh.vacancies.create', [
            'contractTypes' => $contractTypes,
            'statuses' => $statuses,
            'rrhhUsers' => $this->form->getRrhhUsers(),
            'isUpdating' => true,
        ]);
    }
}
