<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderGraphs extends Component
{
    public $chartData = [
        'labels' => [],
        'data' => []
    ];

    public $groupBy = 'month'; // default grouping

    public function mount()
    {
        $this->generateChartData($this->groupBy);
    }

    public function updatedGroupBy($value)
    {
        $this->generateChartData($value);
    }

   public function generateChartData($groupBy)
{
    $query = Order::select(
            DB::raw("
                CASE
                    WHEN '$groupBy' = 'day' THEN DATE(deadline)
                    WHEN '$groupBy' = 'week' THEN DATE_FORMAT(deadline, '%Y-%u')
                    WHEN '$groupBy' = 'month' THEN DATE_FORMAT(deadline, '%Y-%m')
                    WHEN '$groupBy' = 'year' THEN DATE_FORMAT(deadline, '%Y')
                    ELSE DATE(deadline)
                END as period
            "),
            DB::raw("COUNT(*) as total")
        )
        ->where('isActive', 1)
        ->groupBy('period')
        ->orderBy('period')
        ->get();

    $this->chartData = [
        'labels' => $query->pluck('period')->toArray(),
        'data' => $query->pluck('total')->toArray(),
    ];
}


    public function render()
    {
        return view('livewire.order-graphs');
    }
}
