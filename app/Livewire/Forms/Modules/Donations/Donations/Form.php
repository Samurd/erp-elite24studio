<?php

namespace App\Livewire\Forms\Modules\Donations\Donations;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Donation;
use App\Models\Campaign;
use Illuminate\Support\Facades\Auth;

class Form extends LivewireForm
{
    public $name;
    public $campaign_id;
    public $amount; // Will be stored as integer (cents) for money-input component
    public $payment_method;
    public $date;
    public $certified = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'campaign_id' => 'nullable|exists:campaigns,id',
        'amount' => 'required|integer|min:0',
        'payment_method' => 'required|string|max:255',
        'date' => 'required|date',
        'certified' => 'boolean',
    ];

    protected $messages = [
        'amount.required' => 'El monto es obligatorio.',
        'amount.integer' => 'El monto debe ser un número entero (en centavos).',
        'amount.min' => 'El monto no puede ser negativo.',
    ];

    public function store()
    {
        $this->validate();

        $donation = Donation::create([
            'name' => $this->name,
            'campaign_id' => $this->campaign_id,
            'amount' => $this->amount, // Already in cents from money-input
            'payment_method' => $this->payment_method,
            'date' => $this->date,
            'certified' => $this->certified,
        ]);

        return $donation;
    }

    public function update(Donation $donation)
    {
        $this->validate();

        $donation->update([
            'name' => $this->name,
            'campaign_id' => $this->campaign_id,
            'amount' => $this->amount, // Already in cents from money-input
            'payment_method' => $this->payment_method,
            'date' => $this->date,
            'certified' => $this->certified,
        ]);

        session()->flash('success', 'Donación actualizada exitosamente.');

        return redirect()->route('donations.donations.index');
    }

    public function setDonation(Donation $donation)
    {
        $this->name = $donation->name;
        $this->campaign_id = $donation->campaign_id;
        $this->amount = $donation->amount; // Already in cents from database
        $this->payment_method = $donation->payment_method;
        $this->date = $donation->date?->format('Y-m-d');
        $this->certified = $donation->certified;
    }
}