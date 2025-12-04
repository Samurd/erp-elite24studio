<?php

namespace App\Livewire\Modules\Finances\Expense\Components;

use App\Models\Expense;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;

class Totals extends Component
{
    protected $listeners = [
        "refresh-totals" => "mount",
    ];

    public $totalDirect = 0;
    public $totalIndirect = 0;
    public $totalTaxes = 0;
    public $totalFinance = 0;

    public function mount()
    {
        $categoryType = TagCategory::where("slug", "categoria_gasto")->first();

        if (!$categoryType) {
            return;
        }

        // Buscar los tags (categorías de gasto)
        $tagDirect = Tag::where("category_id", $categoryType->id)
            ->where("name", "Costos Directos")
            ->first();
        $tagIndirect = Tag::where("category_id", $categoryType->id)
            ->where("name", "Gastos Indirectos")
            ->first();
        $tagTaxes = Tag::where("category_id", $categoryType->id)
            ->where("name", "Impuestos")
            ->first();
        $tagFinance = Tag::where("category_id", $categoryType->id)
            ->where("name", "Gastos Financieros")
            ->first();

        // Calcular los totales
        $this->totalDirect = $tagDirect
            ? Expense::where("category_id", $tagDirect->id)->sum("amount")
            : 0;
        $this->totalIndirect = $tagIndirect
            ? Expense::where("category_id", $tagIndirect->id)->sum("amount")
            : 0;
        $this->totalTaxes = $tagTaxes
            ? Expense::where("category_id", $tagTaxes->id)->sum("amount")
            : 0;
        $this->totalFinance = $tagFinance
            ? Expense::where("category_id", $tagFinance->id)->sum("amount")
            : 0;
    }

    public function render()
    {
        return view("livewire.modules.finances.expense.components.totals");
    }
}
