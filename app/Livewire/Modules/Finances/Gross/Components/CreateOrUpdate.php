<?php

namespace App\Livewire\Modules\Finances\Gross\Components;

use App\Livewire\Forms\Modules\Finances\Gross\Form;
use App\Models\Income;
use App\Models\Permission;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Livewire\Component;

class CreateOrUpdate extends Component
{
    // public $showModal = false;
    public ?Income $income = null;
    public Form $form;
    // protected $listeners = [
    //     "open-create-modal" => "openModal",
    //     "open-edit-modal" => "openEditModal",
    //     "close-create-modal" => "closeModal",
    //     "files-updated" => '$refresh',
    // ];

    public $users;

    public $income_types;
    public $categories;

    public $results;

    public function mount(Income $income)
    {
        // dd('Mounted');
        if ($income && $income->exists) {
            $this->income = $income;
            $this->form->setIncome($income);
        } else {
            $this->form->date = now()->format("Y-m-d");
        }

        $type_type = TagCategory::where("slug", "tipo_ingreso")->first();
        $category_type = TagCategory::where(
            "slug",
            "categoria_ingreso",
        )->first();
        $result_type = TagCategory::where("slug", "resultado_ingreso")->first();

        $this->income_types =
            Tag::where("category_id", $type_type?->id)->get() ?? [];
        $this->categories =
            Tag::where("category_id", $category_type?->id)->get() ?? [];
        $this->results =
            Tag::where("category_id", $result_type?->id)->get() ?? [];

        $this->users = $this->getUsersProperty();
    }

    public function getUsersProperty()
    {
        $policyAreaPermissionIds = Permission::whereHas("area", function ($query, ) {
            $query->where("slug", "finanzas");
        })->pluck("id");

        return User::whereHas("roles.permissions", function ($query) use ($policyAreaPermissionIds, ) {
            $query->whereIn("permissions.id", $policyAreaPermissionIds);
        })
            ->orderBy("name")
            ->get();
    }

    public function save()
    {
        if (isset($this->income)) {
            $this->form->update();
        } else {
            $this->form->store();
        }

        return redirect()->route('finances.gross.index');
    }

    public function render()
    {
        return view(
            "livewire.modules.finances.gross.components.create-or-update",
        );
    }
}
