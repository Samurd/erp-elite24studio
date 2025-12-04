<?php

namespace App\Livewire\Modules\CaseRecord;

use App\Models\CaseRecord;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{

    use WithPagination;

    protected $listeners = ['delete' => '$refresh'];
    public ?CaseRecord $selectedRecord = null;

    // Modal
    public $isDeleteModalOpen = false;


    // 🔍 Filters
    public $search = '';
    public $tipo_caso = '';
    public $estado = '';
    public $canal = '';
    public $asesor = '';
    public $fecha = '';

    // 📄 Control de página
    protected $paginationTheme = 'tailwind';

    // 🌐 Sincroniza filtros con la URL
    protected $queryString = [
        'search' => ['except' => ''],
        'asesor' => ['except' => ''],
        'tipo_caso' => ['except' => ''],
        'estado' => ['except' => ''],
        'fecha' => ['except' => ''],
        'canal' => ['except' => ''],
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
            'canal',
            'tipo_caso',
            'estado',
            'asesor',
            'fecha',
        ]);
        $this->resetPage();
    }


    public function render()
    {

        $query = CaseRecord::query()->with(['contact', 'status', 'assignedTo']);

        // 🔍 Filtros de búsqueda global
        if ($this->search) {
            $query->where(function ($q) {
                $q->orWhereHas('assignedTo', function ($userQuery) {
                    $userQuery->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
                $q->orWhereHas('contact', function ($contactQuery) {
                    $contactQuery->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
                });
            });
        }

        if ($this->canal) {
            $query->where('channel', 'like', "%{$this->canal}%");
        }


        // 🟢 Filtro por estado
        if ($this->estado) {
            $query->where('status_id', $this->estado);
        }

        // 👤 Filtro por responsable
        if ($this->asesor) {
            $query->where('assigned_to_id', $this->asesor);
        }

        if ($this->tipo_caso) {
            $query->where('case_type_id', $this->tipo_caso);
        }

        if ($this->fecha) {
            $query->where('date', $this->fecha);
        }


        // ⚙️ Aseguramos que existan siempre estos datos
        $state = TagCategory::where('slug', 'estado_caso')->first();
        $case_type = TagCategory::where('slug', 'tipo_caso')->first();
        $states = Tag::where('category_id', $state->id)->get();
        $case_types = Tag::where('category_id', $case_type->id)->get();

        return view('livewire.modules.case-record.index', [
            'states' => $states,
            'case_types' => $case_types,
            'users'    => User::all(),
            'records' => $query->latest()->paginate(10),
        ]);
    }

    public function openDeleteModal($recordId)
    {
        $this->selectedRecord = CaseRecord::find($recordId);

        $this->isDeleteModalOpen = true;
    }

    public function deleteRecord()
    {
        $this->selectedRecord->delete();
        $this->isDeleteModalOpen = false;
        $this->selectedRecord = null;

        $this->dispatch('delete');



        session()->flash('message', 'Contacto eliminado correctamente.');
    }
}
