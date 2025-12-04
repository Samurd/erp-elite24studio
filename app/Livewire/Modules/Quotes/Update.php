<?php

namespace App\Livewire\Modules\Quotes;

use Livewire\Component;
use App\Livewire\Forms\Modules\Quotes\Form;
use App\Models\Quote;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\TagCategory;

class Update extends Component
{
    use \Livewire\WithFileUploads;

    public Form $form;
    public Quote $quote;

    public function mount(Quote $quote)
    {
        $this->quote = $quote;
        $this->form->setQuote($quote);
    }


    public function save()
    {


        $this->form->update();

        session()->flash('success', 'Cotización actualizada exitosamente.');

        return redirect()->route('quotes.index');
    }

    public function render()
    {
        $contacts = Contact::orderBy('name')->get();

        $statusCategory = TagCategory::where('slug', 'estado_cotizacion')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.quotes.create', [
            'contacts' => $contacts,
            'statusOptions' => $statusOptions,
        ]);
    }
}
