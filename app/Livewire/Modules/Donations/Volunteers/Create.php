<?php

namespace App\Livewire\Modules\Donations\Volunteers;

use Livewire\Component;
use App\Models\Volunteer;
use App\Models\Campaign;
use App\Models\Tag;
use App\Models\TagCategory;

class Create extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $country = '';
    public $role = '';
    public $campaign_id = '';
    public $status_id = '';
    public $certified = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|unique:volunteers,email',
        'phone' => 'nullable|string|unique:volunteers,phone',
        'address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'state' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'role' => 'nullable|string|max:255',
        'campaign_id' => 'nullable|exists:campaigns,id',
        'status_id' => 'nullable|exists:tags,id',
        'certified' => 'boolean',
    ];

    protected $listeners = [
        // 1. ESCUCHAR AL HIJO: Cuando termine de subir los archivos
        'attachments-committed' => 'finishCreation'
    ];

    public function save()
    {
        $this->validate();

        $volunteer = Volunteer::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'role' => $this->role,
            'campaign_id' => $this->campaign_id ?: null,
            'status_id' => $this->status_id ?: null,
            'certified' => $this->certified,
        ]);

        $this->dispatch('commit-attachments', [
            'id' => $volunteer->id,
            'name' => $volunteer->name
        ]);


    }


    /**
     * Este método se ejecuta automáticamente cuando el hijo termina
     */
    public function finishCreation()
    {
        // 4. Ahora sí, redirigimos o mostramos éxito
        session()->flash('success', 'Registro creado y archivos adjuntados correctamente.');

        return redirect()->route('donations.volunteers.index');
    }

    public function render()
    {
        $campaigns = Campaign::all();

        $statusCategory = TagCategory::where('slug', 'estado_voluntario')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.donations.volunteers.create', [
            'campaigns' => $campaigns,
            'statusOptions' => $statusOptions,
        ]);
    }
}
