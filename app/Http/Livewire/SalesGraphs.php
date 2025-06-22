<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesGraphs extends Component
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


// public function generateChartData($groupBy)
// {
//     $query = Order::select(
//             DB::raw("
//                 CASE
//                     WHEN '$groupBy' = 'day' THEN DATE(created_at)
//                     WHEN '$groupBy' = 'week' THEN YEAR(created_at) * 100 + WEEK(created_at, 1)
//                     WHEN '$groupBy' = 'month' THEN DATE_FORMAT(created_at, '%Y-%m')
//                     WHEN '$groupBy' = 'year' THEN DATE_FORMAT(created_at, '%Y')
//                     ELSE DATE(created_at)
//                 END as period
//             "),
//             DB::raw("MIN(created_at) as date_for_label"),
//             DB::raw("COUNT(*) as total")
//         )
//         ->where('isActive', 1)
//         ->groupBy('period')
//         ->orderBy('period')
//         ->get();

//     $labels = [];
//     $data = [];

//     foreach ($query as $row) {
//         $date = Carbon::parse($row->date_for_label);

//         switch ($groupBy) {
//             case 'day':
//                 $label = $date->format('n-j') . '-' . $row->total; // e.g., 6-22-4
//                 break;

//             case 'week':
//                 $label = $date->format('M-Y') . ' (Week ' . $date->format('W') . ')'; // e.g., Jun-2025 (Week 25)
//                 break;

//             case 'month':
//                 $label = $date->format('M Y'); // e.g., Jun 2025
//                 break;

//             case 'year':
//                 $label = $date->format('Y'); // e.g., 2025
//                 break;

//             default:
//                 $label = $row->period;
//                 break;
//         }

//         $labels[] = $label;
//         $data[] = $row->total;
//     }

//     $this->chartData = [
//         'labels' => $labels,
//         'data' => $data,
//     ];

//     $this->dispatchBrowserEvent('update-chart', [
//         'labels' => $labels,
//         'data' => $data,
//     ]);


public function generateChartData($groupBy)
{
    $rawGroup = "
        CASE
            WHEN '$groupBy' = 'day' THEN DATE(created_at)
            WHEN '$groupBy' = 'week' THEN YEAR(created_at) * 100 + WEEK(created_at, 1)
            WHEN '$groupBy' = 'month' THEN DATE_FORMAT(created_at, '%Y-%m')
            WHEN '$groupBy' = 'year' THEN DATE_FORMAT(created_at, '%Y')
            ELSE DATE(created_at)
        END
    ";

    $query = payment::select(
            DB::raw("$rawGroup as period"),
            DB::raw("MIN(created_at) as date_for_label"),
            DB::raw("SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_total"),
            DB::raw("SUM(CASE WHEN payment_status != 'paid' THEN 1 ELSE 0 END) as collection_total")
        )
        ->where('isActive', 1)
        ->groupBy('period')
        ->orderBy('period')
        ->get();

    $labels = [];
    $paidData = [];
    $collectionData = [];

    foreach ($query as $row) {
        $date = Carbon::parse($row->date_for_label);

        switch ($groupBy) {
            case 'day':
                $label = $date->format('n-j') . '-' . ($row->paid_total + $row->collection_total);
                break;
            case 'week':
                $label = $date->format('M-Y') . ' (Week ' . $date->format('W') . ')';
                break;
            case 'month':
                $label = $date->format('M Y');
                break;
            case 'year':
                $label = $date->format('Y');
                break;
            default:
                $label = $row->period;
                break;
        }

        $labels[] = $label;
        $paidData[] = $row->paid_total;
        $collectionData[] = $row->collection_total;
    }

    $this->chartData = [
        'labels' => $labels,
        'datasets' => [
            [
                'label' => 'Paid',
                'data' => $paidData,
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                'fill' => false,
                'tension' => 0.3
            ],
            [
                'label' => 'Collection',
                'data' => $collectionData,
                'borderColor' => 'rgba(255, 99, 132, 1)',
                'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                'fill' => false,
                'tension' => 0.3
            ]
        ]
    ];

    $this->dispatchBrowserEvent('update-chart', $this->chartData);
}


    public function render()
    {
        return view('livewire.sales-graphs');
    }
}
