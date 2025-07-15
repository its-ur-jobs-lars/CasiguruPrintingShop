<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Sale;

class ExportSales extends Component
{
    public function render()
    {
        return view('livewire.export-sales');
    }

    public $exportType = '';



public function exportSales()
{
    if (!$this->exportType) {
        session()->flash('error', 'Please select an export type.');
        return;
    }

    $now = Carbon::now();

    switch ($this->exportType) {
        case 'weekly':
            $start = $now->startOfWeek();
            $end = $now->endOfWeek();
            break;
        case 'monthly':
            $start = $now->startOfMonth();
            $end = $now->endOfMonth();
            break;
        case 'yearly':
            $start = $now->startOfYear();
            $end = $now->endOfYear();
            break;
        default:
            session()->flash('error', 'Invalid export type selected.');
            return;
    }

    $sales = Sale::whereBetween('created_at', [$start, $end])->get();

    if ($sales->isEmpty()) {
        session()->flash('error', 'No sales records found for the selected period.');
        return;
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

   

    // Data rows
    $row = 2;
    foreach ($sales as $sale) {
        $sheet->setCellValue("A{$row}", $sale->order_id);
        $sheet->setCellValue("B{$row}", $sale->jo_number);
        $sheet->setCellValue("C{$row}", $sale->name);
        $sheet->setCellValue("D{$row}", $sale->category);
        $sheet->setCellValue("E{$row}", $sale->subcategory);
        $sheet->setCellValue("F{$row}", $sale->qty);
        $sheet->setCellValue("G{$row}", $sale->price);
        $sheet->setCellValue("H{$row}", $sale->payment_status);
        $sheet->setCellValue("I{$row}", $sale->total);
        $row++;
    }

    $filename = "Sales_{$this->exportType}_" . now()->format('Ymd_His') . ".xlsx";
    $directory = storage_path('app/reports');
    if (!file_exists($directory)) mkdir($directory, 0755, true);
    $filePath = "{$directory}/{$filename}";

    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);

    return response()->download($filePath)->deleteFileAfterSend();
}

}
