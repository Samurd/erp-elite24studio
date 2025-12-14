<?php

namespace App\Livewire\Modules\Marketing\Cases;

use App\Livewire\Forms\Modules\Marketing\Cases\Form;
use App\Models\Project;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Livewire\Component;

class Create extends Component
{
    public Form $form;

    public function save()
    {
        $this->form->store();

        return redirect()->route('marketing.cases.index');
    }

    public function getUsers()
    {
        return \App\Services\PermissionCacheService::getUsersByArea('marketing');
    }

    public function render()
    {
        // Obtener opciones para los filtros usando TagCategory
        $typeCategory = TagCategory::where('slug', 'tipo_caso_mk')->first();
        $typeOptions = $typeCategory ? Tag::where('category_id', $typeCategory->id)->get() : collect();

        $statusCategory = TagCategory::where('slug', 'estado_caso_mk')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        // Obtener proyectos
        $projects = Project::orderBy('name')->get();

        // Obtener usuarios para el selector de responsables
        $users = $this->getUsers();

        return view('livewire.modules.marketing.cases.create', [
            'typeOptions' => $typeOptions,
            'statusOptions' => $statusOptions,
            'projects' => $projects,
            'users' => $users,
        ]);
    }
}
