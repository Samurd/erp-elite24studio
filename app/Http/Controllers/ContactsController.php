<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ContactsController extends Controller
{
    public function index(Request $request)
    {
        $query = Contact::query()->with(['status', 'responsible', 'contactType', 'relationType', 'source', 'label']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email_personal', 'like', "%{$request->search}%")
                    ->orWhere('email_corporativo', 'like', "%{$request->search}%");
            });
        }

        if ($request->empresa) {
            $query->where('company', 'like', "%{$request->empresa}%");
        }



        if ($request->responsable) {
            $query->where('responsible_id', $request->responsable);
        }

        if ($request->etiqueta) {
            $query->where('label_id', $request->etiqueta);
        }

        $contacts = $query->latest()->paginate(10)->withQueryString();

        // Data for filters
        $labelCategory = TagCategory::where('slug', 'etiqueta_contacto')->first();
        $labels = $labelCategory ? Tag::where('category_id', $labelCategory->id)->get() : [];

        $users = \App\Services\CommonDataCacheService::getAllUsers();

        return Inertia::render('Contacts/Index', [
            'contacts' => $contacts,
            'labels' => $labels,
            'users' => $users,
            'filters' => $request->only(['search', 'empresa', 'responsable', 'etiqueta']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Contacts/Form', [
            'isEdit' => false,
            'options' => $this->getFormOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email_personal' => 'nullable|email',
            'email_corporativo' => 'nullable|email',
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
        ]);

        Contact::create($data);

        return redirect()->route('contacts.index')->with('success', 'Contacto creado correctamente');
    }

    public function edit(Contact $contact)
    {
        return Inertia::render('Contacts/Form', [
            'isEdit' => true,
            'contact' => $contact,
            'options' => $this->getFormOptions(),
        ]);
    }

    public function update(Request $request, Contact $contact)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email_personal' => 'nullable|email',
            'email_corporativo' => 'nullable|email',
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
        ]);

        $contact->update($data);

        return redirect()->route('contacts.index')->with('success', 'Contacto actualizado correctamente');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('contacts.index')->with('success', 'Contacto eliminado correctamente');
    }

    private function getFormOptions()
    {
        $contact_type = TagCategory::where('slug', "tipo_contacto")->first();
        $relation_type = TagCategory::where('slug', "tipo_relacion")->first();
        $state = TagCategory::where('slug', "estado_contacto")->first();
        $source = TagCategory::where('slug', "fuente")->first();
        $label = TagCategory::where('slug', "etiqueta_contacto")->first();

        return [
            'contact_types' => $contact_type ? Tag::where("category_id", $contact_type->id)->get() : [],
            'relation_types' => $relation_type ? Tag::where("category_id", $relation_type->id)->get() : [],
            'states' => $state ? Tag::where("category_id", $state->id)->get() : [],
            'sources' => $source ? Tag::where("category_id", $source->id)->get() : [],
            'labels' => $label ? Tag::where("category_id", $label->id)->get() : [],
            'users' => \App\Services\CommonDataCacheService::getAllUsers(),
        ];
    }
}
