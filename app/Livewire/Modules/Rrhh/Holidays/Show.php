<?php

namespace App\Livewire\Modules\Rrhh\Holidays;

use App\Models\Holiday;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Show extends Component
{
    use WithPagination;

    public Holiday $holiday;

    // Historical filters
    public $historicalYear = '';
    public $historicalPerPage = 10;

    public function mount(Holiday $holiday)
    {
        $this->holiday = $holiday;
        $this->historicalYear = date('Y');
    }

    public function updatingHistoricalYear()
    {
        $this->resetPage();
    }

    public function delete()
    {
        $this->holiday->delete();

        session()->flash('success', 'Festivo eliminado exitosamente');

        return redirect()->route('rrhh.holidays.index');
    }

    public function render()
    {
        // Get historical data - all holidays for the selected year
        $historicalQuery = Holiday::with(['employee', 'type', 'status', 'approver'])
            ->whereYear('start_date', $this->historicalYear)
            ->orderBy('start_date', 'desc');

        $historicalData = $historicalQuery->paginate($this->historicalPerPage);

        // Calculate statistics for the year
        $yearlyStats = Holiday::whereYear('start_date', $this->historicalYear)->get();

        $totalDays = $yearlyStats->sum(function ($holiday) {
            $start = Carbon::parse($holiday->start_date);
            $end = Carbon::parse($holiday->end_date);
            return $start->diffInDays($end) + 1;
        });

        $lastDate = $yearlyStats->max('end_date');
        $requestsInYear = $yearlyStats->count();

        // Year options for filter
        $yearOptions = range(date('Y') - 5, date('Y') + 2);

        return view('livewire.modules.rrhh.holidays.show', [
            'historicalData' => $historicalData,
            'totalDays' => $totalDays,
            'lastDate' => $lastDate ? Carbon::parse($lastDate)->format('d/m/Y') : '-',
            'requestsInYear' => $requestsInYear,
            'yearOptions' => $yearOptions,
        ]);
    }
}
