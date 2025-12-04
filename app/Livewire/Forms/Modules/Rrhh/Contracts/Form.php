<?php

namespace App\Livewire\Forms\Modules\Rrhh\Contracts;

use App\Models\Contract;
use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;

class Form extends LivewireForm
{
    public ?Contract $contract = null;

    #[Validate('required|exists:employees,id')]
    public $employee_id = '';

    #[Validate('required|exists:tags,id')]
    public $type_id = ''; // Tipo de contrato

    #[Validate('required|exists:tags,id')]
    public $category_id = ''; // Categoria de contrato (Tipo de Relacion)

    #[Validate('required|exists:tags,id')]
    public $status_id = ''; // Estado de contrato

    #[Validate('required|date')]
    public $start_date = '';

    #[Validate('nullable|date|after_or_equal:start_date')]
    public $end_date = '';

    #[Validate('nullable|numeric|min:0')]
    public $amount = '';

    #[Validate('nullable|string|max:255')]
    public $schedule = '';


    public function setContract(Contract $contract)
    {
        $this->contract = $contract;
        $this->employee_id = $contract->employee_id;
        $this->type_id = $contract->type_id;
        $this->category_id = $contract->category_id;
        $this->status_id = $contract->status_id;
        $this->start_date = $contract->start_date ? $contract->start_date->format('Y-m-d') : '';
        $this->end_date = $contract->end_date ? $contract->end_date->format('Y-m-d') : '';
        $this->amount = $contract->amount;
        $this->schedule = $contract->schedule;
    }

    public function store()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $contract = Contract::create([
                'employee_id' => $this->employee_id,
                'type_id' => $this->type_id,
                'category_id' => $this->category_id,
                'status_id' => $this->status_id,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date ?: null,
                'amount' => $this->amount,
                'schedule' => $this->schedule,
                'registered_by_id' => Auth::id(),
            ]);


            DB::commit();
            return $contract;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $this->contract->update([
                'employee_id' => $this->employee_id,
                'type_id' => $this->type_id,
                'category_id' => $this->category_id,
                'status_id' => $this->status_id,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date ?: null,
                'amount' => $this->amount,
                'schedule' => $this->schedule,
            ]);


            DB::commit();
            return $this->contract;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
