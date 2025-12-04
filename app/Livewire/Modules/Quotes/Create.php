<?php

namespace App\Livewire\Modules\Quotes;

use Livewire\Component;
use App\Livewire\Forms\Modules\Quotes\Form;
use Livewire\WithFileUploads;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\TagCategory;

class Create extends Component
{
    use WithFileUploads;

    public Form $form;



    protected $listeners = [
        'attachments-committed' => 'finishCreation'

    ];


    public function save()
    {


        $quote = $this->form->store();

        $nameQuote = "Cotizacion id: {$quote->id}";

        $this->dispatch('commit-attachments', [
            'id' => $quote->id,
            'name' => $nameQuote
        ]);

    }

    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

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
