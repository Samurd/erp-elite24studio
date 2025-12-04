<?php

namespace App\Livewire\Modules\Licenses;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\License;
use App\Models\Tag;
use App\Models\TagCategory;
use App\Models\Project;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $license_type_filter = '';
    public $status_filter = '';
    public $entity_filter = '';
    public $company_filter = '';
    public $requires_extension_filter = '';
    public $date_from = '';
    public $date_to = '';
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
        'license_type_filter' => ['except' => ''],
        'status_filter' => ['except' => ''],
        'entity_filter' => ['except' => ''],
        'company_filter' => ['except' => ''],
        'requires_extension_filter' => ['except' => ''],
        'date_from' => ['except' => ''],
        'date_to' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingLicenseTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingEntityFilter()
    {
        $this->resetPage();
    }

    public function updatingCompanyFilter()
    {
        $this->resetPage();
    }

    public function updatingRequiresExtensionFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search',
            'license_type_filter',
            'status_filter',
            'entity_filter',
            'company_filter',
            'requires_extension_filter',
            'date_from',
            'date_to',
        ]);
        $this->resetPage();
    }

    public function delete($id)
    {
        $license = License::find($id);
        
        if ($license) {
            $license->delete();
            session()->flash('success', 'Licencia eliminada exitosamente.');
        }
    }

    public function render()
    {
        $query = License::with([
            'project',
            'type',
            'status'
        ]);

        // Search by entity, company, or eradicated number
        if ($this->search) {
            $query->where('entity', 'like', '%' . $this->search . '%')
                  ->orWhere('company', 'like', '%' . $this->search . '%')
                  ->orWhere('eradicated_number', 'like', '%' . $this->search . '%');
        }

        // Filter by license type
        if ($this->license_type_filter) {
            $query->where('license_type_id', $this->license_type_filter);
        }

        // Filter by status
        if ($this->status_filter) {
            $query->where('status_id', $this->status_filter);
        }

        // Filter by entity
        if ($this->entity_filter) {
            $query->where('entity', 'like', '%' . $this->entity_filter . '%');
        }

        // Filter by company
        if ($this->company_filter) {
            $query->where('company', 'like', '%' . $this->company_filter . '%');
        }

        // Filter by requires extension
        if ($this->requires_extension_filter !== '') {
            $query->where('requires_extension', $this->requires_extension_filter);
        }

        // Filter by date range (expiration date)
        if ($this->date_from) {
            $query->where('expiration_date', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->where('expiration_date', '<=', $this->date_to);
        }

        $licenses = $query->orderBy('created_at', 'desc')
                           ->paginate($this->perPage);

        // Obtener opciones para los filtros usando TagCategory
        $licenseTypeCategory = TagCategory::where('slug', 'tipo_licencia')->first();
        $licenseTypeOptions = $licenseTypeCategory ? Tag::where('category_id', $licenseTypeCategory->id)->get() : collect();
        
        $statusCategory = TagCategory::where('slug', 'estado_licencia')->first();
        $statusOptions = $statusCategory ? Tag::where('category_id', $statusCategory->id)->get() : collect();

        return view('livewire.modules.licenses.index', [
            'licenses' => $licenses,
            'licenseTypeOptions' => $licenseTypeOptions,
            'statusOptions' => $statusOptions,
        ]);
    }
}
