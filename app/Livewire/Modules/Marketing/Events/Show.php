<?php

namespace App\Livewire\Modules\Marketing\Events;

use App\Models\Event;
use App\Models\EventItem;
use App\Models\Tag;
use App\Models\TagCategory;
use Livewire\Component;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public Event $event;
    
    // Filters for event items
    public $search = '';
    public $unitFilter = '';
    public $perPage = 10;
    
    protected $paginationTheme = 'tailwind';

    public function mount(Event $event)
    {
        $this->event = $event;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedUnitFilter()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function delete($itemId)
    {
        $item = EventItem::findOrFail($itemId);
        $item->delete();
        
        session()->flash('success', 'Ítem eliminado exitosamente.');
    }

    public function render()
    {
        // Get unit options for filter
        $unitCategory = TagCategory::where('slug', 'unidad')->first();
        $unitOptions = $unitCategory ? Tag::where('category_id', $unitCategory->id)->get() : collect();

        // Query event items with filters
        $itemsQuery = $this->event->items()->with('unit');

        // Apply search filter
        if ($this->search) {
            $itemsQuery->where('description', 'like', '%' . $this->search . '%');
        }

        // Apply unit filter
        if ($this->unitFilter) {
            $itemsQuery->where('unit_id', $this->unitFilter);
        }

        // Get paginated results
        $items = $itemsQuery->paginate($this->perPage);

        return view('livewire.modules.marketing.events.show', [
            'event' => $this->event,
            'items' => $items,
            'unitOptions' => $unitOptions,
        ]);
    }
}
