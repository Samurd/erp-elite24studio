<?php

namespace App\Livewire\Modules\Rrhh\Interviews;

use App\Livewire\Forms\Modules\Rrhh\Interviews\Form;
use App\Models\Applicant;
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
        
        return redirect()->route('rrhh.interviews.index');
    }
    
    public function render()
    {
        // Obtener opciones para los filtros usando TagCategory
        $statusCategory = TagCategory::where('slug', 'estado_entrevista')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();
        
        $interviewTypeCategory = TagCategory::where('slug', 'tipo_entrevista')->first();
        $interviewTypeOptions = $interviewTypeCategory ? Tag::where('category_id', $interviewTypeCategory->id)->get() : collect();
        
        $resultCategory = TagCategory::where('slug', 'resultado_entrevista')->first();
        $resultOptions = $resultCategory ? Tag::where('category_id', $resultCategory->id)->get() : collect();
        
        // Obtener usuarios con permiso al área de RRHH
        $rrhhPermissionIds = \App\Models\Permission::whereHas("area", function ($query) {
            $query->where("slug", "rrhh");
        })->pluck("id");

        $interviewerOptions = \App\Models\User::whereHas("roles.permissions", function ($query) use ($rrhhPermissionIds) {
            $query->whereIn("permissions.id", $rrhhPermissionIds);
        })
        ->orderBy("name")
        ->get();

        $applicants = Applicant::orderBy('full_name')->get();

        return view('livewire.modules.rrhh.interviews.create', [
            'statusOptions' => $statusOptions,
            'interviewTypeOptions' => $interviewTypeOptions,
            'resultOptions' => $resultOptions,
            'interviewerOptions' => $interviewerOptions,
            'applicants' => $applicants,
        ]);
    }
}
