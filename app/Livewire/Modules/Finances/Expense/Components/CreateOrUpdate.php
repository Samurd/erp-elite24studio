<?php

namespace App\Livewire\Modules\Finances\Expense\Components;

use App\Livewire\Forms\Modules\Finances\Expense\Form;
use App\Models\Expense;
use App\Models\Permission;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Livewire\Component;

class CreateOrUpdate extends Component
{
    // public $showModal = false;
    public ?Expense $expense = null;
    public Form $form;
    // protected $listeners = [
    //     "open-create-modal" => "openModal",
    //     "open-edit-modal" => "openEditModal",
    //     "close-create-modal" => "closeModal",
    //     "files-updated" => '$refresh',
    // ];

    public $users;

    // public $income_types;
    public $categories;

    public $results;

    public function mount(Expense $expense)
    {
        if ($expense && $expense->exists) {
            $this->expense = $expense;
            $this->form->setExpense($expense);
        } else {
            $this->form->date = now()->format("Y-m-d");
        }

        // $type_type = TagCategory::where("slug", "tipo_ingreso")->first();
        $category_type = TagCategory::where("slug", "categoria_gasto")->first();
        $result_type = TagCategory::where("slug", "resultado_ingreso")->first();

        // $this->income_types =
        //     Tag::where("category_id", $type_type?->id)->get() ?? [];
        $this->categories =
            Tag::where("category_id", $category_type?->id)->get() ?? [];
        $this->results =
            Tag::where("category_id", $result_type?->id)->get() ?? [];

        $this->users = $this->getUsersProperty();
    }

    public function getUsersProperty()
    {
        return \App\Services\PermissionCacheService::getUsersByArea('finanzas');
    }

    public function save()
    {
        if (isset($this->expense)) {
            $this->form->update();
        } else {
            $this->form->store();
        }

        return redirect()->route('finances.expenses.index');
    }
    public function render()
    {
        return view(
            "livewire.modules.finances.expense.components.create-or-update",
        );
    }
}
