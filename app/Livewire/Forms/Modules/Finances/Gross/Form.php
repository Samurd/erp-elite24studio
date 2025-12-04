<?php

namespace App\Livewire\Forms\Modules\Finances\Gross;

use App\Models\Income;
use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;

class Form extends LivewireForm
{
    public ?Income $income = null;

    #[Validate("required|string")]
    public $name;

    #[Validate("required")]
    public $type_id;

    #[Validate("nullable")]
    public $category_id;

    #[Validate("nullable|string")]
    public $description;

    #[Validate("required")]
    public $date;

    #[Validate("required|integer|min:0")]
    public $amount;

    #[Validate("required")]
    public $created_by_id;

    #[Validate("required")]
    public $result_id;

    public function setIncome(Income $income)
    {
        $this->income = $income;
        $this->name = $income->name;
        $this->type_id = $income->type_id;
        $this->category_id = $income->category_id;
        $this->description = $income->description;
        $this->date = $income->date;
        $this->amount = $income->amount;
        $this->result_id = $income->result_id;
    }

    public function resetForm()
    {
        $this->clearValidation();
        $this->reset();
    }

    public function update()
    {
        $this->validate();

        $this->income->update([
            "name" => $this->name,
            "type_id" => $this->type_id,
            "category_id" => $this->category_id,
            "description" => $this->description,
            "date" => $this->date,
            "amount" => $this->amount,
            "created_by_id" => $this->created_by_id,
            "result_id" => $this->result_id,
        ]);

        $this->income = null;

        $this->resetForm();
    }

    public function store()
    {
        $this->validate();

        Income::create([
            "name" => $this->name,
            "type_id" => $this->type_id,
            "category_id" => $this->category_id,
            "description" => $this->description,
            "date" => $this->date,
            "amount" => $this->amount,
            "created_by_id" => $this->created_by_id,
            "result_id" => $this->result_id,
        ]);

        $this->resetForm();
    }
}
