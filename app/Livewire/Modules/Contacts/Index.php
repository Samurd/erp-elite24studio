<?php

namespace App\Livewire\Modules\Contacts;

use App\Models\Contact;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $listeners = ['delete' => '$refresh'];
    public ?Contact $selectedContact = null;


    // Modal

    public $isInfoModalOpen = false;
    public $isDeleteModalOpen = false;



    // 🔍 Filters
    public $search = '';
    public $tipo_contacto = '';
    public $tipo_relacion = '';
    public $estado = '';
    public $etiqueta = '';
    public $empresa = '';
    public $responsable = '';
    public $fecha_primer_contacto = '';
    public $fuente = '';

    // 📄 Control de página
    protected $paginationTheme = 'tailwind';

    // 🌐 Sincroniza filtros con la URL
    protected $queryString = [
        'search' => ['except' => ''],
        'tipo_contacto' => ['except' => ''],
        'tipo_relacion' => ['except' => ''],
        'estado' => ['except' => ''],
        'etiqueta' => ['except' => ''],
        'empresa' => ['except' => ''],
        'responsable' => ['except' => ''],
        'fecha_primer_contacto' => ['except' => ''],
        'fuente' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    // 🔄 Reinicia la paginación al cambiar filtros
    public function updating($field)
    {
        $this->resetPage();
    }

    // 🔁 Limpia filtros
    public function resetFilters()
    {
        $this->reset([
            'search',
            'tipo_contacto',
            'tipo_relacion',
            'estado',
            'etiqueta',
            'empresa',
            'responsable',
            'fecha_primer_contacto',
            'fuente'
        ]);
        $this->resetPage();
    }


    public function render()
    {
        $query = Contact::query();

        // 🔍 Filtros
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        if ($this->empresa) {
            $query->where('company', 'like', "%{$this->empresa}%");
        }

        if ($this->estado) {
            $query->where('status_id', $this->estado);
        }

        if ($this->responsable) {
            $query->where('responsible_id', $this->responsable);
        }

        // ⚙️ Aseguramos que existan siempre estos datos
        $state = TagCategory::where('slug', 'estado_contacto')->first();
        $states = $state ? Tag::where('category_id', $state->id)->get() : collect();

        return view('livewire.modules.contacts.index', [
            'contacts' => $query->latest()->paginate(10),
            'states' => $states,
            'users' => \App\Services\CommonDataCacheService::getAllUsers(),
        ]);
    }


    public function openContactModal($contactId)
    {
        $this->selectedContact = Contact::find($contactId);
        $this->isInfoModalOpen = true;
    }

    public function closeContactModal()
    {
        $this->isInfoModalOpen = false;
        // $this->selectedContact = null;
    }
    public function openDeleteModal($contactId)
    {
        $this->selectedContact = Contact::find($contactId);

        $this->isDeleteModalOpen = true;
    }

    public function deleteContact()
    {
        $this->selectedContact->delete();
        $this->isInfoModalOpen = false;
        $this->isDeleteModalOpen = false;
        $this->selectedContact = null;

        $this->dispatch('delete');



        session()->flash('message', 'Contacto eliminado correctamente.');
    }
}
