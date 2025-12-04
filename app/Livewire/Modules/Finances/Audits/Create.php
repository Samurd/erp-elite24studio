<?php

namespace App\Livewire\Modules\Finances\Audits;

use App\Livewire\Forms\Modules\Finances\Audits\Form;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Services\AreaPermissionService;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public Form $form;

    protected $listeners = [
        // 1. ESCUCHAR AL HIJO: Cuando termine de subir los archivos
        'attachments-committed' => 'finishCreation'
    ];


    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('finances.audits.index');
    }

    public function save()
    {

        $audit = $this->form->store();

        $auditName = "Auditoria con id: " . $audit->id;

        $this->dispatch('commit-attachments', [
            'id' => $audit->id,
            'name' => $auditName
        ]);
    }

    public function getAuditTypesProperty()
    {
        $category = TagCategory::where('slug', 'tipo_auditoria')->first();
        if (!$category)
            return collect();

        return Tag::where('category_id', $category->id)->get()->filter(function ($tag) {
            // Check permission to create audits of this type
            if (empty($tag->slug))
                return false;
            return AreaPermissionService::canArea('create', $tag->slug);
        });
    }

    public function getAuditStatusesProperty()
    {
        $category = TagCategory::where('slug', 'estado_auditoria')->first();
        return $category ? Tag::where('category_id', $category->id)->get() : collect();
    }

    public function render()
    {
        return view('livewire.modules.finances.audits.create', [
            'auditTypes' => $this->auditTypes,
            'auditStatuses' => $this->auditStatuses,
        ]);
    }
}
