<?php

namespace App\Livewire\Forms\Modules\Quotes;

use Livewire\Attributes\Validate;
use Livewire\Form as LivewireForm;
use App\Models\Quote;

class Form extends LivewireForm
{
    public ?Quote $quote = null;

    #[Validate('nullable|exists:contacts,id')]
    public $contact_id = null;

    #[Validate('required|date')]
    public $issued_at = '';

    #[Validate('nullable|exists:tags,id')]
    public $status_id = null;

    #[Validate('nullable|integer|min:0')]
    public $total = null;

    #[Validate('nullable|exists:users,id')]
    public $user_id = null;


    public function setQuote(Quote $quote)
    {
        $this->quote = $quote;
        $this->contact_id = $quote->contact_id;
        $this->issued_at = $quote->issued_at ? $quote->issued_at->format('Y-m-d') : '';
        $this->status_id = $quote->status_id;
        $this->total = $quote->total;
        $this->user_id = $quote->user_id;
    }

    public function store()
    {
        $this->validate();

        $quote = Quote::create([
            'contact_id' => $this->contact_id,
            'issued_at' => $this->issued_at,
            'status_id' => $this->status_id,
            'total' => $this->total,
            'user_id' => $this->user_id ?? auth()->id(),
        ]);

        return $quote;



    }

    public function update()
    {
        $this->validate();

        $this->quote->update([
            'contact_id' => $this->contact_id,
            'issued_at' => $this->issued_at,
            'status_id' => $this->status_id,
            'total' => $this->total,
            'user_id' => $this->user_id ?? auth()->id(),
        ]);

    }


}
