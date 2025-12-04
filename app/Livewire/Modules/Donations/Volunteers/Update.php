<?php

namespace App\Livewire\Modules\Donations\Volunteers;

use Livewire\Component;
use App\Models\Volunteer;
use App\Models\Campaign;
use App\Models\Tag;
use App\Models\TagCategory;

class Update extends Component
{
    public $volunteer;
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

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:volunteers,email,' . $this->volunteer->id,
            'phone' => 'nullable|string|unique:volunteers,phone,' . $this->volunteer->id,
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'role' => 'nullable|string|max:255',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'status_id' => 'nullable|exists:tags,id',
            'certified' => 'boolean',
        ];
    }

    public function mount(Volunteer $volunteer)
    {
        $this->volunteer = $volunteer;
        $this->name = $volunteer->name;
        $this->email = $volunteer->email;
        $this->phone = $volunteer->phone;
        $this->address = $volunteer->address;
        $this->city = $volunteer->city;
        $this->state = $volunteer->state;
        $this->country = $volunteer->country;
        $this->role = $volunteer->role;
        $this->campaign_id = $volunteer->campaign_id;
        $this->status_id = $volunteer->status_id;
        $this->certified = $volunteer->certified;
    }

    public function update()
    {
        $this->validate();

        $this->volunteer->update([
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

        session()->flash('success', 'Voluntario actualizado exitosamente.');

        return redirect()->route('donations.volunteers.index');
    }

    public function render()
    {
        $campaigns = Campaign::all();
        
        $statusCategory = TagCategory::where('slug', 'estado_voluntario')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect([]);

        return view('livewire.modules.donations.volunteers.update', [
            'campaigns' => $campaigns,
            'statusOptions' => $statusOptions,
        ]);
    }
}
