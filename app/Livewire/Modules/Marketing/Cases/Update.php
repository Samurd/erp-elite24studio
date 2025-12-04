<?php

namespace App\Livewire\Modules\Marketing\Cases;

use App\Livewire\Forms\Modules\Marketing\Cases\Form;
use App\Models\CaseMarketing;
use App\Models\Project;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Livewire\Component;

class Update extends Component
{
    public Form $form;
    public CaseMarketing $caseMarketing;

    public function mount(CaseMarketing $caseMarketing)
    {
        $this->caseMarketing = $caseMarketing;
        $this->form->setCaseMarketing($caseMarketing);
    }

    public function save()
    {
        $this->form->update($this->caseMarketing);
        
        return redirect()->route('marketing.cases.index');
    }
    
    public function getUsers()
    {
        // Obtener permisos del área de Marketing
        $marketingPermissionIds = \App\Models\Permission::whereHas("area", function ($query) {
            $query->where("slug", "marketing");
        })->pluck("id");

        return \App\Models\User::whereHas("roles.permissions", function ($query) use ($marketingPermissionIds) {
            $query->whereIn("permissions.id", $marketingPermissionIds);
        })
            ->orderBy("name")
            ->get();
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
            'caseMarketing' => $this->caseMarketing,
        ]);
    }
}
