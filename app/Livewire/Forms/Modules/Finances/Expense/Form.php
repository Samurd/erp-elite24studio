<?php

namespace App\Livewire\Forms\Modules\Finances\Expense;

use App\Models\Expense;
use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;

class Form extends LivewireForm
{
    public ?Expense $expense = null;

    #[Validate("required|string")]
    public $name;

    #[Validate("required")]
    public $category_id;

    #[Validate("nullable|string")]
    public $description;

    #[Validate("required")]
    public $date;

    #[Validate("required|numeric")]
    public $amount;

    #[Validate("required")]
    public $created_by_id;

    #[Validate("required")]
    public $result_id;

    public function setExpense(Expense $expense)
    {
        $this->expense = $expense;
        $this->name = $expense->name;
        // $this->type_id = $expense->type_id;
        $this->category_id = $expense->category_id;
        $this->description = $expense->description;
        $this->date = $expense->date;
        $this->amount = $expense->amount;
        $this->result_id = $expense->result_id;
    }

    public function resetForm()
    {
        $this->clearValidation();
        $this->reset();
    }

    public function update()
    {
        $this->validate();

        $this->expense->update([
            "name" => $this->name,
            "category_id" => $this->category_id,
            "description" => $this->description,
            "date" => $this->date,
            "amount" => $this->amount,
            "created_by_id" => $this->created_by_id,
            "result_id" => $this->result_id,
        ]);

        $this->expense = null;

        $this->resetForm();
    }

    public function store()
    {
        $this->validate();

        Expense::create([
            "name" => $this->name,
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
