<?php

namespace App\Livewire\Modules\Contacts;

use App\Models\Contact;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateOrUpdate extends Component
{
    public $name;
    public $email;
    public $company;
    public $contact_type_id;
    public $relation_type_id;
    public $status_id;
    public $address;
    public $phone;
    public $city;
    public $source_id;
    public $first_contact_date;
    public $responsible_id;
    public $label_id; // etiqueta (Lead nuevo, etc...)
    public $notes;




    public $isEdit = false;
    public ?Contact $selectedContact = null;
    public $contact_types;
    public $relation_types;
    public $states;
    public $sources;

    public $defaultUserId;
    public $users;
    public $labels;


    function getRules()
    {
        if ($this->isEdit) {
            return [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:contacts,email,' . $this->selectedContact->id,
                'company' => 'required|string|max:255',
                'contact_type_id' => 'required|integer',
                'relation_type_id' => 'required|integer',
                'status_id' => 'required|integer',
                'address' => 'required|string',
                'phone' => 'required|max:20',
                'city' => 'nullable|string|max:100',
                'source_id' => 'required|integer',
                'first_contact_date' => 'required|date',
                'responsible_id' => 'required|integer',
                'label_id' => 'required|integer',
                'notes' => 'nullable|string',

            ];
        }

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts',
            'company' => 'required|string|max:255',
            'contact_type_id' => 'required|integer',
            'relation_type_id' => 'required|integer',
            'status_id' => 'required|integer',
            'address' => 'required|string',
            'phone' => 'required|max:20',
            'city' => 'nullable|string|max:100',
            'source_id' => 'required|integer',
            'first_contact_date' => 'required|date',
            'responsible_id' => 'required|integer',
            'label_id' => 'required|integer',
            'notes' => 'nullable|string',

        ];
    }

    public function save()
    {
        logger()->info('=== INICIO SAVE CONTACT ===');
        logger()->info('isEdit: ' . ($this->isEdit ? 'true' : 'false'));

        try {
            logger()->info('Validando datos...');
            $validateData = $this->validate($this->getRules());
            logger()->info('Datos validados correctamente:', $validateData);

            if ($this->isEdit) {
                logger()->info('Actualizando contacto existente...');
                $this->selectedContact->update($validateData);
                logger()->info('Contacto actualizado correctamente');
            } else {
                logger()->info('Creando nuevo contacto...');
                $contact = Contact::create($validateData);
                logger()->info('Contacto creado correctamente con ID: ' . $contact->id);
            }

            session()->flash('message', 'Contacto guardado correctamente.');
            logger()->info('=== FIN SAVE CONTACT EXITOSO ===');

            return redirect()->route('contacts.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            logger()->error('Error de validación:', $e->errors());
            session()->flash('error', 'Error de validación: ' . json_encode($e->errors()));
            return;
        } catch (\Exception $e) {
            logger()->error('Error al guardar contacto: ' . $e->getMessage());
            logger()->error('Stack trace: ' . $e->getTraceAsString());
            session()->flash('error', 'Error: ' . $e->getMessage());
            return;
        }
    }
    public function mount(?Contact $contact)
    {

        if ($contact && $contact->exists) {
            $this->isEdit = true;

            $this->selectedContact = $contact;
            $this->name = $contact->name;
            $this->company = $contact->company;
            $this->email = $contact->email;

            $this->phone = $contact->phone;
            $this->address = $contact->address;
            $this->city = $contact->city;
            $this->contact_type_id = $contact->contact_type_id;
            $this->relation_type_id = $contact->relation_type_id;
            $this->status_id = $contact->status_id;
            $this->source_id = $contact->source_id;
            $this->responsible_id = $contact->responsible_id;
            $this->label_id = $contact->label_id;
            $this->first_contact_date = $contact->first_contact_date;
            $this->notes = $contact->notes;
        }

        $current_user = Auth::user();

        $contact_type = TagCategory::where('slug', "tipo_contacto")->first();
        $relation_type = TagCategory::where('slug', "tipo_relacion")->first();
        $state = TagCategory::where('slug', "estado_contacto")->first();
        $source = TagCategory::where('slug', "fuente")->first();
        $label = TagCategory::where('slug', "etiqueta_contacto")->first();

        $this->defaultUserId = $current_user->id;
        $this->responsible_id = $current_user->id;
        $this->first_contact_date = date("Y-m-d");


        $this->contact_types = Tag::where("category_id", $contact_type->id)->get();
        $this->relation_types = Tag::where("category_id", $relation_type->id)->get();
        $this->states = Tag::where("category_id", $state->id)->get();
        $this->sources = Tag::where("category_id", $source->id)->get();
        $this->users = \App\Services\CommonDataCacheService::getAllUsers();
        $this->labels = Tag::where("category_id", $label->id)->get();
    }
    public function render()
    {
        return view('livewire.modules.contacts.create-or-update');
    }
}
