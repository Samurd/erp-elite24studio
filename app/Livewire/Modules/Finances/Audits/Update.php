<?php

namespace App\Livewire\Modules\Finances\Audits;

use App\Livewire\Forms\Modules\Finances\Audits\Form;
use App\Models\Audit;
use App\Models\File;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Services\AreaPermissionService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Update extends Component
{
    use WithFileUploads;

    public Form $form;
    public Audit $audit;


    public function mount(Audit $audit)
    {
        $this->audit = $audit;
        $this->form->setAudit($audit);
    }

    public function save()
    {
        $this->form->update();

        session()->flash('success', 'Auditoría actualizada exitosamente');

        return redirect()->route('finances.audits.index');
    }

    public function getAuditTypesProperty()
    {
        $category = TagCategory::where('slug', 'tipo_auditoria')->first();
        if (!$category)
            return collect();

        return Tag::where('category_id', $category->id)->get()->filter(function ($tag) {
            // Check permission to edit audits of this type
            // Or at least view them if editing doesn't require specific type permission check here
            // Assuming 'update' permission on the area
            if (empty($tag->slug))
                return false;
            return AreaPermissionService::canArea('update', $tag->slug);
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
