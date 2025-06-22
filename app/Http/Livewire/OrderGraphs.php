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

//   public function generateChartData($groupBy)
// {
//     $query = Order::select(
//             DB::raw("DATE(created_at) as date"),
//             DB::raw("COUNT(*) as total")
//         )
//         ->where('isActive', 1)
//         ->groupBy(DB::raw("DATE(created_at)"))
//         ->orderBy('date')
//         ->get();

//     $labels = [];
//     $data = [];

//     foreach ($query as $row) {
//         $date = Carbon::parse($row->date);
//         $formatted = $date->format('n-j'); // e.g., 6-22
//         $labels[] = $formatted . '-' . $row->total; // e.g., 6-22-4
//         $data[] = $row->total;
//     }

//     $this->chartData = [
//         'labels' => $labels,
//         'data' => $data,
//     ];
// }

public function generateChartData($groupBy)
{
    $query = Order::select(
            DB::raw("
                CASE
                    WHEN '$groupBy' = 'day' THEN DATE(created_at)
                    WHEN '$groupBy' = 'week' THEN YEAR(created_at) * 100 + WEEK(created_at, 1)
                    WHEN '$groupBy' = 'month' THEN DATE_FORMAT(created_at, '%Y-%m')
                    WHEN '$groupBy' = 'year' THEN DATE_FORMAT(created_at, '%Y')
                    ELSE DATE(created_at)
                END as period
            "),
            DB::raw("MIN(created_at) as date_for_label"),
            DB::raw("COUNT(*) as total")
        )
        ->where('isActive', 1)
        ->groupBy('period')
        ->orderBy('period')
        ->get();

    $labels = [];
    $data = [];

    foreach ($query as $row) {
        $date = Carbon::parse($row->date_for_label);

        switch ($groupBy) {
            case 'day':
                $label = $date->format('n-j') . '-' . $row->total; // e.g., 6-22-4
                break;

            case 'week':
                $label = $date->format('M-Y') . ' (Week ' . $date->format('W') . ')'; // e.g., Jun-2025 (Week 25)
                break;

            case 'month':
                $label = $date->format('M Y'); // e.g., Jun 2025
                break;

            case 'year':
                $label = $date->format('Y'); // e.g., 2025
                break;

            default:
                $label = $row->period;
                break;
        }

        $labels[] = $label;
        $data[] = $row->total;
    }

    $this->chartData = [
        'labels' => $labels,
        'data' => $data,
    ];

    $this->dispatchBrowserEvent('update-chart', [
        'labels' => $labels,
        'data' => $data,
    ]);
}



    public function render()
    {
        return view('livewire.order-graphs');
    }
}
