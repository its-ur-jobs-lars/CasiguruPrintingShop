<?php

namespace App\Http\Livewire;

use Livewire\Component;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;
use App\Models\RequestReceipt;

class ExportRecieptExcel extends Component
{

    public function export($orderReceiptId)
    {
        // Fetch the receipt by order_receipt_id
        $receipt = OrderReceipt::where('order_receipt_id', $orderReceiptId)->firstOrFail();

        // Load the template Excel file
        $templatePath = storage_path('app/templates/receipt_template.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Map data to specific cells
        $sheet->setCellValue('6BC', $receipt->name);
        $sheet->setCellValue('7BC', $receipt->address);
        $sheet->setCellValue('6GH', $receipt->payment_date);
        $sheet->setCellValue('7GH', $receipt->contact_no);
        $sheet->setCellValue('8GH', $receipt->order_id);

        // Start writing order items from row 11
        $startRow = 11;
        $endRow = $startRow;

        foreach (OrderReceipt::where('order_id', $receipt->order_id)->get() as $item) {
            $sheet->setCellValue("A{$endRow}", $item->qty);
            $sheet->setCellValue("B{$endRow}", $item->category_id);
            $sheet->setCellValue("C{$endRow}", $item->subcategory_id);
            $sheet->setCellValue("E{$endRow}", $item->price);
            $sheet->setCellValue("G{$endRow}", $item->amount);
            $endRow++;
        }

        // Summary fields
        $sheet->setCellValue("GH27", $receipt->total);
        $sheet->setCellValue("GH28", $receipt->payment);
        $sheet->setCellValue("GH29", $receipt->balance);

        $sheet->setCellValue("CD31", $receipt->payment_method);
        $sheet->setCellValue("CD32", $receipt->reference_number);
        $sheet->setCellValue("CD33", $receipt->date);

        $sheet->setCellValue("GH31", $receipt->payment_status);
        $sheet->setCellValue("GH32", $receipt->remarks);
        $sheet->setCellValue("GH33", $receipt->service_by);

        // Create a temporary file
        $tempFile = storage_path('app/public/order_receipt_export.xlsx');

        // Save the spreadsheet
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        // Return the file as a download response
        return response()->download($tempFile)->deleteFileAfterSend();
    }

    public function render()
    {
        return view('livewire.export-reciept-excel');
    }
}
