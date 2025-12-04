<?php

namespace App\Livewire\Modules\Rrhh\Contracts;

use App\Models\Contract;
use App\Models\File;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class Show extends Component
{
    public Contract $contract;

    public function mount(Contract $contract)
    {
        $this->contract = $contract->load(['employee', 'type', 'category', 'status', 'files', 'registeredBy']);
    }

    public function downloadFile($fileId)
    {
        $file = File::findOrFail($fileId);
        
        if (Storage::disk('public')->exists($file->path)) {
            return Storage::disk('public')->download($file->path, $file->name);
        }
    }

    public function render()
    {
        return view('livewire.modules.rrhh.contracts.show');
    }
}
